<?php

use Illuminate\Support\Facades\Route;

Route::redirect('', 'login');

Route::middleware(['auth'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', \App\Http\Controllers\UserController::class);
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
    Route::resource('policy-holders', \App\Http\Controllers\PolicyHolderController::class);
    
    Route::post('country-list', [\App\Helpers\Helper::class, 'getCountries'])->name('country-list');
    Route::post('state-list', [\App\Helpers\Helper::class, 'getStatesByCountry'])->name('state-list');
    Route::post('city-list', [\App\Helpers\Helper::class, 'getCitiesByState'])->name('city-list');
    Route::post('user-list', [\App\Helpers\Helper::class, 'getUsers'])->name('user-list');
    Route::post('holder-list', [\App\Helpers\Helper::class, 'getHolders'])->name('holder-list');
    Route::post('insured-list', [\App\Helpers\Helper::class, 'getInsureds'])->name('insured-list');
    Route::post('document-list', [\App\Helpers\Helper::class, 'getDocuments'])->name('document-list');


    Route::get('get-docs', [\App\Http\Controllers\CaseController::class, 'getDocs'])->name('get-docs');

    Route::get('settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    Route::get('cases/create/{id?}', [\App\Http\Controllers\CaseController::class, 'create'])->name('cases.create');
    Route::get('cases/edit/{id?}', [\App\Http\Controllers\CaseController::class, 'edit'])->name('cases.edit');
    Route::get('cases', [\App\Http\Controllers\CaseController::class, 'index'])->name('cases.index');

    Route::post('cases/submission', [\App\Http\Controllers\CaseController::class, 'submission'])->name('case.submission');
    Route::post('cases/auto-save', [\App\Http\Controllers\CaseController::class, 'autoSave'])->name('case.auto-save');
    Route::get('cases/get-communications', [\App\Http\Controllers\CaseController::class, 'getCommunications'])->name('case.get-communications');
    Route::get('cases/get-case-file-notes', [\App\Http\Controllers\CaseController::class, 'getCaseFileNotes'])->name('case.get-case-file-notes');
    Route::post('cases/get-insured-lives', [\App\Http\Controllers\CaseController::class, 'getInsuredLives'])->name('case.getInsuredLives');
    Route::post('cases/get-insured-lives-sidebar', [\App\Http\Controllers\CaseController::class, 'getInsuredLivesSidebar'])->name('case.getInsuredLivesSidebar');
    Route::post('cases/get-insured-life', [\App\Http\Controllers\CaseController::class, 'getInsuredLife'])->name('case.getInsuredLife');
    Route::post('cases/delete-insured-life', [\App\Http\Controllers\CaseController::class, 'deleteInsuredLife'])->name('case.deleteInsuredLife');
    Route::post('cases/get-beneficiaries', [\App\Http\Controllers\CaseController::class, 'getBeneficiaries'])->name('case.getBeneficiaries');
    Route::post('cases/get-beneficiary', [\App\Http\Controllers\CaseController::class, 'getBeneficiary'])->name('case.getBeneficiary');
    Route::post('cases/delete-beneficiary', [\App\Http\Controllers\CaseController::class, 'deleteBeneficiary'])->name('case.deleteBeneficiary');

    Route::post('upload-document', [\App\Http\Controllers\CaseController::class, 'uploadDoc'])->name('upload-document');
});