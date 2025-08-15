<?php

namespace App\Http\Controllers;

use App\Models\PolicyDocument;
use App\Models\PolicyCommunication;
use App\Models\PolicyCaseFileNote;
use Illuminate\Http\Request;
use App\Models\Policy;

class CaseController extends Controller
{
    public function create(Request $request, $id = null)
    {
        $user = auth()->id();
        $policy = null;

        if (!empty($id)) {
            try {
                $id = decrypt($id);
                $id = explode('***', $id);

                if (count($id) == 3 && $id[2] == 'sha-2') {
                    if (!empty($id[1]) && Policy::find($id[1])) {
                        $policy = Policy::find($id[1]);
                    } else {
                        $policy = new Policy();
                        $policy->opening_date = now();
                        $policy->added_by = $user;
                        $policy->save();

                        session()->put(['new_policy' => [
                            'user_id' => $user,
                            'policy_id' => $policy->id
                        ]]);

                        return redirect()->route('cases.create', encrypt($user . '***' . $policy->id . '***sha-2'));
                    }
                }

            } catch (\Exception $e) {
                return redirect()->route('cases.index');
            }
        } else {
            return redirect()->route('cases.index');
        }

        $title = 'Case Management';
        $subTitle = 'Add New Case';

        return view('cases.create.index', compact('title', 'subTitle', 'policy'));
    }

    public function edit(Request $request, $id) {

        if (!empty($id)) {
            try {
                $id = decrypt($id);
                $policy = Policy::find($id);
            } catch (\Exception $e) {
                return redirect()->route('cases.index');
            }
        } else {
            return redirect()->route('cases.index');
        }

        $title = 'Case Management';
        $subTitle = 'Edit Case';

        return view('cases.create.index', compact('title', 'subTitle', 'policy'));
    }

    public function index(Request $request) 
    {
        if ($request->ajax()) {
            return $this->ajax();
        }

        $title = 'Case Management';
        $subTitle = 'Cases';

        return view('cases.index', compact('title', 'subTitle'));
    }

    public function ajax() {
        $policy = Policy::query();

        if (request()->filled('filter_case')) {
            $policy->where('policy_number', 'LIKE', "%" . request('filter_case') . "%");
        }

        if (request()->filled('filter_opened')) {
            $policy->where('opening_date', date('Y-m-d', strtotime(request('filter_opened'))));
        }

        if (request()->filled('filter_holder')) {
            $policy->whereHas('holders', fn ($builder) => $builder->where('name', 'LIKE', '%' . request('filter_holder') . '%'));
        }

        if (request()->filled('filter_introducer')) {
            $policy->whereHas('introducers', fn ($builder) => $builder->where('name', 'LIKE', '%' . request('filter_introducer') . '%'));
        }
        
        if (request()->filled('filter_status')) {
            $policy->where('status', request('filter_status'));
        }

        return datatables()
        ->eloquent($policy)
        ->editColumn('opening_date', fn ($row) => date('Y-m-d', strtotime($row->opening_date)))
        ->addColumn('theholder', fn ($row) => isset($row?->holders[0]?->holder?->name) ? $row?->holders[0]?->holder?->name : 'N/A')
        ->addColumn('introducer', fn ($row) => isset($row->introducers[0]->name) ? $row->introducers[0]->name : 'N/A')
        ->editColumn('status', function ($row) {
            if ($row->status == 0) {
                return '<span class="btn draft"> Draft </span>';
            } else if ($row->status == 1) {
                return '<span class="btn pending"> Pending </span>';
            } else if ($row->status == 2) {
                return '<span class="btn follow-up"> Follow Up </span>';
            } else if ($row->status == 3) {
                return '<span class="btn active"> Active </span>';
            } else if ($row->status == 4) {
                return '<span class="btn inactive"> In Active </span>';
            }
        })
        ->editColumn('action', function ($row) {
            $html = '';

            if (auth()->user()->can('cases.edit')) {
                $html .= '<ul>
                    <li><a href="' . route('cases.edit', encrypt($row->id)) . '"> View </a></li>
                    <li><a href="' . route('cases.edit', encrypt($row->id)) . '"> Edit</a></li>
                </ul>';
            }

            return $html;
        })
        ->addIndexColumn()
        ->rawColumns(['status', 'action'])
        ->toJson();
    }

    public function submission(\App\Services\PolicyService $service) 
    {
        $submission = $service->submit(request());

        if (isset($submission['errors'])) {
            return response()->json($submission, 422);
        }

        $submission['timestamp'] = now()->format('H:i:s');

        return response()->json($submission);
    }

    public function getDocs(Request $request) {
        $html = '<input type="hidden" name="adding" value="1" />';

        foreach (\App\Models\Document::where('status', $request->status)->get() as $status) {
            $html .= '
            <div class="mb-2 row">
                <div class="col-8">
                    <input type="checkbox" name="documents[]" id="doc-' . $status->id . '" value="' . $status->id . '"> &nbsp;&nbsp;&nbsp;
                    <label for="doc-' . $status->id . '">' . $status->title . '</label> 
                </div>
                <div class="col-4">

                </div>
            </div>
            ';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function autoSave(\App\Services\PolicyService $service) 
    {
        $request = request();
        
        $request->merge(['silent_save' => 1]);
        
        $request->merge(['save' => 'draft']);
        
        $submission = $service->submit($request, true);

        if (isset($submission['errors'])) {
            return response()->json($submission, 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Auto-saved successfully',
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    public function uploadDoc(Request $request) {
        $request->validate([
            'file' => 'required|file|max:10240',
            'policy_id' => 'required|integer',
            'doc_id' => 'required|integer'
        ]);

        $folder = 'kyc-docs';

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($folder)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($folder);
        }

        $filename = time().'_'.$request->file->getClientOriginalName();
        $path = \Illuminate\Support\Facades\Storage::disk('public')->putFileAs($folder, $request->file, $filename);
        $shouldCheck = false;

        if (PolicyDocument::where('policy_id', $request->policy_id)
        ->where('document_id', $request->doc_id)->exists()) {
            PolicyDocument::where('policy_id', $request->policy_id)
            ->where('document_id', $request->doc_id)->update([
                'document' => $filename
            ]);
        } else {
            PolicyDocument::create([
                'document_id' => $request->doc_id,
                'policy_id' => $request->policy_id,
                'document_type' => $request->dt_type,
                'document' => $filename,
                'uploaded' => 1
            ]);

            $shouldCheck = true;
        }


        return response()->json([
            'status' => 'success',
            'url' => asset('storage/' . $path),
            'check' => $shouldCheck
        ]);
    }

    public function getCommunications(Request $request) {
        $request->validate([
            'policy' => 'required|integer'
        ]);

        $g1Data = PolicyCommunication::where('policy_id', $request->policy)->get();
        
        $html = '';
        if($g1Data && $g1Data->count() > 0) {
            $html .= '<div class="mb-4">
                <h5>Previous Communication Entries</h5>
                <div class="accordion" id="communicationAccordion">';
            
            foreach($g1Data as $index => $communication) {
                $html .= '<div class="accordion-item">
                    <h2 class="accordion-header" id="heading'.$index.'">
                        <button class="accordion-button '.($index > 0 ? 'collapsed' : '').'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$index.'" aria-expanded="'.($index == 0 ? 'true' : 'false').'" aria-controls="collapse'.$index.'">
                            '.($communication->type ?: 'Communication').' - '.($communication->date ? \Carbon\Carbon::parse($communication->date)->format('M d, Y') : 'No Date').'
                        </button>
                    </h2>
                    <div id="collapse'.$index.'" class="accordion-collapse collapse '.($index == 0 ? 'show' : '').'" aria-labelledby="heading'.$index.'" data-bs-parent="#communicationAccordion">
                        <div class="accordion-body">
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Communication Date:</strong></div>
                                <div class="col-sm-9">'.($communication->date ? \Carbon\Carbon::parse($communication->date)->format('M d, Y H:i') : 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Communication Type:</strong></div>
                                <div class="col-sm-9">'.($communication->type ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Contact Person(s):</strong></div>
                                <div class="col-sm-9">'.($communication->contact_person_involved ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Summary of Discussion:</strong></div>
                                <div class="col-sm-9">'.($communication->summary_of_discussion ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Action Taken/Next Steps:</strong></div>
                                <div class="col-sm-9">'.($communication->action_taken_or_next_step ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Internal Owner(s):</strong></div>
                                <div class="col-sm-9">'.($communication->internal_owners ?: 'N/A').'</div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            
            $html .= '</div></div>';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getCaseFileNotes(Request $request) {
        $request->validate([
            'policy' => 'required|integer'
        ]);

        $g2Data = PolicyCaseFileNote::where('policy_id', $request->policy)->get();
        
        $html = '';
        if($g2Data && $g2Data->count() > 0) {
            $html .= '<div class="mb-4">
                <h5>Previous Case File Notes</h5>
                <div class="accordion" id="caseFileNotesAccordion">';
            
            foreach($g2Data as $index => $note) {
                $html .= '<div class="accordion-item">
                    <h2 class="accordion-header" id="heading'.$index.'">
                        <button class="accordion-button '.($index > 0 ? 'collapsed' : '').'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$index.'" aria-expanded="'.($index == 0 ? 'true' : 'false').'" aria-controls="collapse'.$index.'">
                            '.($note->noted_by ?: 'Note').' - '.($note->date ? \Carbon\Carbon::parse($note->date)->format('M d, Y') : 'No Date').'
                        </button>
                    </h2>
                    <div id="collapse'.$index.'" class="accordion-collapse collapse '.($index == 0 ? 'show' : '').'" aria-labelledby="heading'.$index.'" data-bs-parent="#caseFileNotesAccordion">
                        <div class="accordion-body">
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Date of Note:</strong></div>
                                <div class="col-sm-9">'.($note->date ? \Carbon\Carbon::parse($note->date)->format('M d, Y H:i') : 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Note By:</strong></div>
                                <div class="col-sm-9">'.($note->noted_by ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Note(s):</strong></div>
                                <div class="col-sm-9">'.($note->notes ?: 'N/A').'</div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            
            $html .= '</div></div>';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getBeneficiaries(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $beneficiaries = \App\Models\PolicyBeneficiary::where('policy_id', $request->policy_id)->get();

        $html = '';
        if ($beneficiaries && $beneficiaries->count() > 0) {
            $html .= '<div class="mb-4">'
                .'<div class="accordion" id="beneficiariesAccordion">';

            foreach($beneficiaries as $index => $b) {
                $html .= '<div class="accordion-item" id="bene-reco-' . $b->id . '">'
                    .'<h2 class="accordion-header" id="bHeading'.$index.'">'
                    .'<button class="accordion-button '.($index>0?'collapsed':'').'" type="button" data-bs-toggle="collapse" data-bs-target="#bCollapse'.$index.'" aria-expanded="'.($index==0?'true':'false').'" aria-controls="bCollapse'.$index.'">'
                    .($b->name ?: 'Beneficiary').' - '.($b->date_of_birth ? \Carbon\Carbon::parse($b->date_of_birth)->format('M d, Y') : 'No Date')
                    .'</button>'
                    .'</h2>'
                    .'<div id="bCollapse'.$index.'" class="accordion-collapse collapse '.($index==0?'show':'').'" aria-labelledby="bHeading'.$index.'" data-bs-parent="#beneficiariesAccordion">'
                    .'<div class="accordion-body">'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Name:</strong></div><div class="col-sm-9">'.($b->name ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Address:</strong></div><div class="col-sm-9">'.($b->address ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Country:</strong></div><div class="col-sm-9">'.($b->country ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>City:</strong></div><div class="col-sm-9">'.($b->city ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>ZIP:</strong></div><div class="col-sm-9">'.($b->zip ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Status:</strong></div><div class="col-sm-9">'.(ucfirst($b->status) ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Smoker:</strong></div><div class="col-sm-9">'.(ucfirst(str_replace('-', ' ', $b->smoker_status)) ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Allocation:</strong></div><div class="col-sm-9">'.number_format($b->beneficiary_death_benefit_allocation,2).'%</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Designation:</strong></div><div class="col-sm-9">'.ucfirst($b->designation_of_beneficiary).'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Email:</strong></div><div class="col-sm-9">'.($b->email ?: 'N/A').'</div></div>'
                    .'<div class="row mb-2"><div class="col-sm-3"><strong>Action:</strong></div><div class="col-sm-9">'
                    .'<button type="button" class="btn btn-primary btn-sm" onclick="d1EditBeneficiary('.$b->id.')">Edit</button>&nbsp;&nbsp;'
                    .'<button type="button" class="btn btn-primary btn-sm" onclick="d1DeleteBeneficiary('.$b->id.')">Delete</button>'
                    .'</div></div>'
                    .'</div></div></div>';
            }

            $html .= '</div></div>';
        }

        return response()->json(['html' => $html]);
    }

    public function getBeneficiary(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'beneficiary_id' => 'required|integer'
        ]);

        $beneficiary = \App\Models\PolicyBeneficiary::with('insured')->where('id', $request->beneficiary_id)->first();
        return response()->json(['data' => $beneficiary]);
    }

    public function deleteBeneficiary(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'beneficiary_id' => 'required|integer'
        ]);

        \App\Models\PolicyBeneficiary::query()->delete();
        return response()->json(['status' => true]);
    }

    public function getInsuredLives(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $insuredLives = \App\Models\PolicyInsuredLifeInformation::where('policy_id', $request->policy_id)->get();
        
        $html = '';
        if($insuredLives && $insuredLives->count() > 0) {
            $html .= '<div class="mb-4">
                <div class="accordion" id="insuredLifeAccordion">';
            
            foreach($insuredLives as $index => $insuredLife) {
                $html .= '<div class="accordion-item" id="ins-life-reco-' . $insuredLife->id . '">
                    <h2 class="accordion-header" id="heading'.$index.'">
                        <button class="accordion-button '.($index > 0 ? 'collapsed' : '').'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$index.'" aria-expanded="'.($index == 0 ? 'true' : 'false').'" aria-controls="collapse'.$index.'">
                            '.($insuredLife->name ?: 'Insured Life').' - '.($insuredLife->date_of_birth ? \Carbon\Carbon::parse($insuredLife->date_of_birth)->format('M d, Y') : 'No Date').'
                        </button>
                    </h2>
                    <div id="collapse'.$index.'" class="accordion-collapse collapse '.($index == 0 ? 'show' : '').'" aria-labelledby="heading'.$index.'" data-bs-parent="#insuredLifeAccordion">
                        <div class="accordion-body">
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Name:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->name ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Place of Birth:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->place_of_birth ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Date of Birth:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->date_of_birth ? \Carbon\Carbon::parse($insuredLife->date_of_birth)->format('M d, Y') : 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Address:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->address ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Country:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->country ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>City:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->city ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Postcode/ZIP:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->zip ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Status:</strong></div>
                                <div class="col-sm-9">'.(ucfirst($insuredLife->status) ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Smoker Status:</strong></div>
                                <div class="col-sm-9">'.(ucfirst(str_replace('-', ' ', $insuredLife->smoker_status)) ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Nationality:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->nationality ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Gender:</strong></div>
                                <div class="col-sm-9">'.(ucfirst($insuredLife->gender) ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Country of Legal Residence:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->country_of_legal_residence ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Passport Number:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->passport_number ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Country of Issuance:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->country_of_issuance ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Relationship to Policyholder:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->relationship_to_policyholder ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Email:</strong></div>
                                <div class="col-sm-9">'.($insuredLife->email ?: 'N/A').'</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-3"><strong>Action:</strong></div>
                                <div class="col-sm-9">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="editInsuredLife('.$insuredLife->id.')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="deleteInsuredLife('.$insuredLife->id.')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            
            $html .= '</div></div>';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function getInsuredLife(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'insured_life_id' => 'required|integer'
        ]);

        $insuredLife = \App\Models\PolicyInsuredLifeInformation::where('policy_id', $request->policy_id)
            ->where('id', $request->insured_life_id)
            ->first();

        return response()->json([
            'data' => $insuredLife
        ]);
    }

    public function deleteInsuredLife(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer',
            'insured_life_id' => 'required|integer'
        ]);

        \App\Models\PolicyInsuredLifeInformation::where('policy_id', $request->policy_id)
            ->where('id', $request->insured_life_id)
            ->delete();

        return response()->json(['status' => true]);
    }

    public function getInsuredLivesSidebar(Request $request) {
        $request->validate([
            'policy_id' => 'required|integer'
        ]);

        $insuredLives = \App\Models\PolicyInsuredLifeInformation::where('policy_id', $request->policy_id)->get();
        
        $html = '';
        if($insuredLives && $insuredLives->count() > 0) {
            foreach($insuredLives as $insuredLife) {
                $displayName = $insuredLife->name ?: 'Insured Life';
                $html .= '<li class="insured-life-item" data-id="'.$insuredLife->id.'">
                    <a class="insured-life-link" href="#" data-section="section-c-1" data-insured-id="'.$insuredLife->id.'">
                        <span class="insured-name">'.$displayName.'</span>
                        <div class="insured-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-insured" onclick="editInsuredLifeFromSidebar('.$insuredLife->id.')" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20h9" />
                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                </svg>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-insured" onclick="deleteInsuredLifeFromSidebar('.$insuredLife->id.')" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                                </svg>
                            </button>
                        </div>
                    </a>
                </li>';
            }
        }

        return response()->json([
            'html' => $html
        ]);
    }
}
