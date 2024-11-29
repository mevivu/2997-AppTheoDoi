<?php

namespace App\Traits;


use Closure;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait ResponseController
{
    use UseLog;

    public function handleResponse(Request $request, Closure $updateFunction, string $indexRoute, string $editRoute): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $response = $updateFunction($request);

            if ($response) {
                if ($request->input('submitter') == 'save') {
                    DB::commit();
                    return to_route($editRoute, ['id' => $response->id])->with('success', __('notifySuccess'));
                } else {
                    DB::commit();
                    return to_route($indexRoute)->with('success', __('notifySuccess'));
                }
            }

            DB::rollback();
            return back()->with('error', __('notifyError'));

        } catch (Exception $e) {
            DB::rollback();
            $this->logError("Error during store or update operation", $e);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate a response for updating a resource with transaction handling.
     *
     * @param Request $request The current request instance.
     * @param Closure $updateFunction The closure function that performs the update operation.
     * @return RedirectResponse
     */
    public function handleUpdateResponse(Request $request, Closure $updateFunction): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $response = $updateFunction($request);

            if ($response) {
                DB::commit();
                return back()->with('success', __('notifySuccess'));
            }

            DB::rollback();
            return back()->with('error', __('notifyFail'))->withInput();

        } catch (Exception $e) {
            DB::rollback();
            $this->logError("Error during update operation", $e);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate a response for deleting a resource with transaction handling.
     *
     * @param mixed $id The id of the resource to delete.
     * @param Closure $deleteFunction The closure function that performs the delete operation.
     * @return RedirectResponse
     */
    public function handleDeleteResponse($id, Closure $deleteFunction): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $response = $deleteFunction($id);

            if ($response) {
                DB::commit();
                return back()->with('success', __('notifySuccess'));
            }

            DB::rollback();
            return back()->with('error', __('notifyFail'));

        } catch (Exception $e) {
            DB::rollback();
            $this->logError("Error during delete operation", $e);
            return back()->with('error', __('notifyFail'));
        }
    }


}
