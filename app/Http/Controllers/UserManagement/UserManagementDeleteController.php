<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementDeleteController extends Controller
{
	/**
	 * Remove the specified user from storage.
	 */
	public function __invoke(Request $request, User $user)
	{
		try {
			// Check if admin is trying to delete non-cashier
			if ($request->user()->role === 'admin' && $user->role !== 'cashier') {
				abort(403, 'Unauthorized action.');
			}

			// Prevent deleting yourself
			if ($request->user()->id === $user->id) {
				return redirect()
					->back()
					->with('error', 'You cannot delete your own account.');
			}

			$user->delete();

			return redirect()
				->route('user-management.index')
				->with('success', 'User deleted successfully.');
		} catch (\Exception $e) {
			return redirect()
				->back()
				->with('error', 'Failed to delete user: ' . $e->getMessage());
		}
	}
}
