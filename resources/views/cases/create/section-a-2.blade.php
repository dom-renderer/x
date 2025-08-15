<form id="form-section-a-2">
    <table class="table">
        <thead>
            <tr>
                <th>Role</th>
                <th>Name</th>
                <th>Entity Type</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Policyholder @requiredField </td>
                <td><input type="text" class="form-control" name="policy_holder[name]" value="{{ $keyRolesA['name'] }}" ></td>
                <td><input type="text" class="form-control" name="policy_holder[entity_type]" value="{{ $keyRolesA['entity_type'] }}"></td>
                <td><textarea  class="form-control" rows="1" name="policy_holder[notes]">{{ $keyRolesA['notes'] }}</textarea></td>
            </tr>
            <tr>
                <td>Insured Life @requiredField </td>
                <td><input  type="text" class="form-control" name="unsured_life[name]" value="{{ $keyRolesB['name'] }}"></td>
                <td><input  type="text" class="form-control" name="unsured_life[entity_type]" value="{{ $keyRolesB['entity_type'] }}"></td>
                <td><textarea  class="form-control" rows="1" name="unsured_life[notes]">{{ $keyRolesB['notes'] }}</textarea></td>
            </tr>
            <tr>
                <td>Beneficiary(ies) @requiredField </td>
                <td><input  type="text" class="form-control" name="beneficiaries[name]" value="{{ $keyRolesC['name'] }}"></td>
                <td><input  type="text" class="form-control" name="beneficiaries[entity_type]" value="{{ $keyRolesC['entity_type'] }}"></td>
                <td><textarea  class="form-control" rows="1" name="beneficiaries[notes]">{{ $keyRolesC['notes'] }}</textarea></td>
            </tr>
            <tr>
                <td>Investment Advisor/ Manager @requiredField </td>
                <td><input  type="text" class="form-control" name="advisor[name]" value="{{ $keyRolesD['name'] }}"></td>
                <td><input  type="text" class="form-control" name="advisor[entity_type]" value="{{ $keyRolesD['entity_type'] }}"></td>
                <td><textarea  class="form-control" rows="1" name="advisor[notes]">{{ $keyRolesD['notes'] }}</textarea></td>
            </tr>
            <tr>
                <td>IDF Name (if applicable)</td>
                <td><input type="text" class="form-control" name="idf[name]" value="{{ $keyRolesE['name'] }}"></td>
                <td><input type="text" class="form-control" name="idf[entity_type]" value="{{ $keyRolesE['entity_type'] }}"></td>
                <td><textarea class="form-control" rows="1" name="idf[notes]">{{ $keyRolesE['notes'] }}</textarea></td>
            </tr>
            <tr>
                <td>IDF Manager @requiredField </td>
                <td><input  type="text" class="form-control" name="idfm_holder[name]" value="{{ $keyRolesF['name'] }}"></td>
                <td><input  type="text" class="form-control" name="idfm_holder[entity_type]" value="{{ $keyRolesF['entity_type'] }}"></td>
                <td><textarea  class="form-control" rows="1" name="idfm_holder[notes]">{{ $keyRolesF['notes'] }}</textarea></td>
            </tr>
            <tr>
                <td>Custodian Bank(s) (if applicable)</td>
                <td><input type="text" class="form-control" name="custodian_holder[name]" value="{{ $keyRolesG['name'] }}"></td>
                <td><input type="text" class="form-control" name="custodian_holder[entity_type]" value="{{ $keyRolesG['entity_type'] }}"></td>
                <td><textarea class="form-control" rows="1" name="custodian_holder[notes]">{{ $keyRolesG['notes'] }}</textarea></td>
            </tr>
        </tbody>
    </table>

<div class="mb-3 float-end">
    <button type="submit" data-type="next" data-next="section-b-1" class="btn btn-primary save-next">Save & Next</button>
</div>
</form>