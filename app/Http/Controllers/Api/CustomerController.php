<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function __construct()
    {
        // Resource Permissions handled in CustomerPolicy
        $this->authorizeResource(Customer::class, 'customer');
    }

    public function index(): JsonResponse
    {
        return $this->sendResponse(Customer::all());
    }

    public function show(Customer $customer): JsonResponse
    {
        return $this->sendResponse($customer);
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        // the password is hashed in the prepareForValidation function in UserRequest
        $customer = Customer::create($request->validated());
        $this->handleFileUpload($request, $customer);

        return $this->sendCreated($customer, 'New customer successfully created');
    }

    public function update(CustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->only('name', 'surname'));
        $this->handleFileUpload($request, $customer->fresh());

        return $this->sendResponse($customer->fresh(), 'Customer successfully updated');
    }

    public function destroy(Customer $customer): JsonResponse
    {
        // file will be deleted in the booted method in the Model
        $customer->delete();
        return $this->sendNoContent();
    }

    private function handleFileUpload(CustomerRequest $request, $customer): void
    {
        if ($request->hasFile('photo_file')) {
            // delete the previous one, if exists
            if ($customer->photo_file) {
                Storage::delete($customer->photo_file);
            }
            $customer->photo_file = $request->file('photo_file')->store('public');
            $customer->save();
        }
    }
}
