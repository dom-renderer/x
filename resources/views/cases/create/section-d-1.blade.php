<form id="form-section-d-1">
	<input type="hidden" id="d-1-edit-id" value="">
	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Insured Life @requiredField</label>
			<select class="form-select" id="d-1-insured-life" name="insured_life_id" >
				<option value="">Select</option>
			</select>
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label"> Beneficiary Full Legal Name @requiredField</label>
			<input type="text" class="form-control" id="d-1-name" name="name" >
		</div>
	</div>

	<div class="row">
		<div class="col-md-3 mb-3">
			<label class="form-label">Place Of Birth @requiredField</label>
			<input type="text" class="form-control" id="d-1-place-of-birth" name="place_of_birth" >
		</div>
		<div class="col-md-3 mb-3">
			<label class="form-label">Date Of Birth @requiredField</label>
			<input type="text" class="form-control" id="d-1-dob" name="date_of_birth"  readonly>
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Residential Address @requiredField</label>
			<input type="text" class="form-control" id="d-1-address" name="address" >
		</div>
	</div>

	<div class="row">
		<div class="col-md-4 mb-3">
			<label class="form-label">Country @requiredField</label>
			<input type="text" class="form-control" id="d-1-country" name="country" >
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">City @requiredField</label>
			<input type="text" class="form-control" id="d-1-city" name="city" >
		</div>
		<div class="col-md-4 mb-3">
			<label class="form-label">Postcode/ ZIP @requiredField</label>
			<input type="text" class="form-control" id="d-1-zip" name="zip" >
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Status @requiredField</label>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-status" id="d-1-status-single" value="single" checked><label class="form-check-label" for="d-1-status-single">Single</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-status" id="d-1-status-married" value="married"><label class="form-check-label" for="d-1-status-married">Married</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-status" id="d-1-status-divorced" value="divorced"><label class="form-check-label" for="d-1-status-divorced">Divorced</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-status" id="d-1-status-separated" value="separated"><label class="form-check-label" for="d-1-status-separated">Separated</label></div>
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Smoker Status @requiredField</label>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-smoker" id="d-1-smoker-yes" value="smoker" checked><label class="form-check-label" for="d-1-smoker-yes">Smoker</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-smoker" id="d-1-smoker-no" value="non-smoker"><label class="form-check-label" for="d-1-smoker-no">Non-Smoker</label></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Nationality @requiredField</label>
			<input type="text" class="form-control" id="d-1-nationality" name="nationality" >
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Gender @requiredField</label>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-gender" id="d-1-gender-male" value="male" checked><label class="form-check-label" for="d-1-gender-male">Male</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-gender" id="d-1-gender-female" value="female"><label class="form-check-label" for="d-1-gender-female">Female</label></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Country of Legal Residence @requiredField</label>
			<input type="text" class="form-control" id="d-1-legal-residence" name="country_of_legal_residence" >
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Countries of Tax Residence @requiredField</label>
			<div class="input-group">
				<input type="text" class="form-control" id="d-1-tax-input">
				<button type="button" class="btn btn-success d-1-add-tax">+</button>
				<button type="button" class="btn btn-danger d-1-remove-tax">-</button>
			</div>
			<div id="d-1-tax-list" class="mt-2"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Passport Number @requiredField</label>
			<input type="text" class="form-control" name="passport_number" id="d-1-passport" >
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Country of Issuance @requiredField</label>
			<input type="text" class="form-control" name="country_of_issuance" id="d-1-issuance" >
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Relationship to Policyholder @requiredField</label>
			<input type="text" class="form-control" name="relationship_to_policyholder" id="d-1-relationship" >
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">E-Mail @requiredField</label>
			<input type="email" class="form-control" name="email" id="d-1-email" >
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label class="form-label">Death Benefit Allocation (%) @requiredField</label>
			<input type="number" class="form-control" id="d-1-allocation" name="beneficiary_death_benefit_allocation" min="0" max="100" step="0.01" >
		</div>
		<div class="col-md-6 mb-3">
			<label class="form-label">Designation @requiredField</label>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-designation" id="d-1-designation-revocable" value="revocable" checked><label class="form-check-label" for="d-1-designation-revocable">Revocable</label></div>
			<div class="form-check"><input class="form-check-input" type="radio" name="d-1-designation" id="d-1-designation-irrevocable" value="irrevocable"><label class="form-check-label" for="d-1-designation-irrevocable">Irrevocable</label></div>
		</div>
	</div>

    <div class="row">
        <div class="col-12">
			<button type="submit" data-type="next" data-next="section-e-1" class="btn btn-primary save-next float-end">Save & Next</button>
			<button type="button" class="btn btn-primary d-1-save-add-new me-2 float-end">Save & Add New</button>
        </div>
    </div>
</form>

<!-- <div class="mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
				<div class="card-header">
					<h4 class="card-title">Saved Beneficiary Information</h4>
				</div>
                <div class="card-body">
					<div id="d-1-beneficiaries-accordion" class="mb-4"></div>
                </div>
            </div>
        </div>
    </div>
</div> -->