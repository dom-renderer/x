<form id="form-section-c-1">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="c1_controlling_person_name" class="form-label">Insured Life Full Legal Name @requiredField</label>
            <input type="text" class="form-control" id="c1_controlling_person_name" name="controlling_person_name" >
        </div>
        <div class="col-md-3 mb-3">
            <label for="c1_place_of_birth" class="form-label">Place Of Birth @requiredField</label>
            <input type="text" class="form-control" id="c1_place_of_birth" name="place_of_birth" >
        </div>
        <div class="col-md-3 mb-3">
            <label for="c1_date_of_birth" class="form-label">Date Of Birth @requiredField</label>
            <input type="text" class="form-control" id="c1_date_of_birth" name="date_of_birth" readonly >
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 mb-3">
            <label for="c1address" class="form-label">Residential Address @requiredField</label>
            <input type="text" class="form-control" id="c1address" name="address" >
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="c1country" class="form-label">Country @requiredField</label>
            <input type="text" class="form-control" id="c1country" name="country" >
        </div>
        <div class="col-md-4 mb-3">
            <label for="c1city" class="form-label">City @requiredField</label>
            <input type="text" class="form-control" id="c1city" name="city" >
        </div>
        <div class="col-md-4 mb-3">
            <label for="c1zip" class="form-label">Postcode/ZIP @requiredField</label>
            <input type="text" class="form-control" id="c1zip" name="zip" >
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Status @requiredField</label>
            <div class="form-check">
                <input class="form-check-input c1sts" type="radio" name="status" id="c1status_single" value="single" checked >
                <label class="form-check-label" for="c1status_single">Single</label>
            </div>
            <div class="form-check">
                <input class="form-check-input c1sts" type="radio" name="status" id="c1status_married" value="married" >
                <label class="form-check-label" for="c1status_married">Married</label>
            </div>
            <div class="form-check">
                <input class="form-check-input c1sts" type="radio" name="status" id="c1status_divorced" value="divorced" >
                <label class="form-check-label" for="c1status_divorced">Divorced</label>
            </div>
            <div class="form-check">
                <input class="form-check-input c1sts" type="radio" name="status" id="c1status_separated" value="separated" >
                <label class="form-check-label" for="c1status_separated">Separated</label>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Smoker Status @requiredField</label>
            <div class="form-check">
                <input class="form-check-input c1-smsts" type="radio" name="smoker_status" id="c1smoker_status_smoker" value="smoker" checked >
                <label class="form-check-label" for="c1smoker_status_smoker">Smoker</label>
            </div>
            <div class="form-check">
                <input class="form-check-input c1-smsts" type="radio" name="smoker_status" id="c1smoker_status_non_smoker" value="non-smoker" >
                <label class="form-check-label" for="c1smoker_status_non_smoker">Non-Smoker</label>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="c1nationality" class="form-label">Nationality @requiredField</label>
            <input type="text" class="form-control" id="c1nationality" name="nationality" >
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Gender @requiredField</label>
            <div class="form-check">
                <input class="form-check-input c1-gndr" type="radio" name="gender" id="c1gender_male" value="male" checked >
                <label class="form-check-label" for="c1gender_male">Male</label>
            </div>
            <div class="form-check">
                <input class="form-check-input c1-gndr" type="radio" name="gender" id="c1gender_female" value="female" >
                <label class="form-check-label" for="c1gender_female">Female</label>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="c1country_of_legal_residence" class="form-label">Country of Legal Residence @requiredField</label>
            <input type="text" class="form-control" id="c1country_of_legal_residence" name="country_of_legal_residence" >
        </div>
        <div class="col-md-6 mb-3">
            <label for="c1countries_of_tax_residence" class="form-label">Countries of Tax Residence @requiredField</label>
            <div class="row section-c-1-country-tax-residence-row">
                <div class="col-sm-10">
                    <input type="text" class="form-control section-c-1-country-tax-residence" name="all_countries[]" >
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-success section-c-1-add">+</button>
                    <button type="button" class="btn btn-danger section-c-1-remove">-</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="c1passport_number" class="form-label">Passport Number @requiredField</label>
            <input type="text" class="form-control" id="c1passport_number" name="passport_number" >
        </div>
        <div class="col-md-6 mb-3">
            <label for="c1country_of_issuance" class="form-label">Country of Issuance @requiredField</label>
            <input type="text" class="form-control" id="c1country_of_issuance" name="country_of_issuance" >
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="c1relationship_to_policyholder" class="form-label">Relationship to Policyholder @requiredField</label>
            <input type="text" class="form-control" id="c1relationship_to_policyholder" name="relationship_to_policyholder" >
        </div>
        <div class="col-md-6 mb-3">
            <label for="c1email" class="form-label">E-Mail @requiredField</label>
            <input type="email" class="form-control" id="c1email" name="email" >
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <button type="submit" data-type="next" data-next="section-d-1" class="btn btn-primary save-next float-end">Save & Next</button>
            <button type="button" class="btn btn-primary float-end me-2" id="save-add-new">Save & Add New</button>
        </div>
    </div>
</form>

<!-- <div class="mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">                    
                    <h4 class="card-title">Saved Insured Life Information</h4>
                </div>
                <div class="card-body">
                    <div id="insured-life-accordion" class="mb-4"></div>
                </div>
            </div>
        </div>
    </div>
</div> -->