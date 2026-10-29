<?php
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin-layout')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Action Modal Properties
    public $showConfirmModal = false;
    public $confirmActionUserId = null;
    public $confirmActionType = null;
    public $confirmActionName = null;
    public $confirmActionText = null;
    public $confirmActionBtnText = null;
    public $confirmActionBtnClass = null;

    // Role Modal Properties
    public $showRoleModal = false;
    public $confirmRoleUserId = null;
    public $confirmRoleNewRole = null;
    public $confirmRoleName = null;
    public $confirmRoleText = null;
    public $confirmRoleBtnText = null;
    public $confirmRoleBtnClass = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function prepareRoleChange($userId, $role)
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('notify', type: 'error', message: "Only admins can change roles.");
            return;
        }

        $user = User::find($userId);
        if (!$user) return;

        $this->confirmRoleUserId = $userId;
        $this->confirmRoleNewRole = $role;
        $this->confirmRoleName = $user->name;

        if ($role === 'admin') {
            $this->confirmRoleText = "Are you sure you want to make {$user->name} an Admin? They will receive an email notification.";
            $this->confirmRoleBtnText = 'Make Admin';
            $this->confirmRoleBtnClass = 'bg-purple-600 hover:bg-purple-700 focus:ring-purple-500';
        } elseif ($role === 'moderator') {
            $this->confirmRoleText = "Are you sure you want to make {$user->name} a Moderator? They will receive an email notification.";
            $this->confirmRoleBtnText = 'Make Moderator';
            $this->confirmRoleBtnClass = 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500';
        } else {
            $this->confirmRoleText = "Are you sure you want to demote {$user->name} to a regular User? No email will be sent.";
            $this->confirmRoleBtnText = 'Demote User';
            $this->confirmRoleBtnClass = 'bg-gray-600 hover:bg-gray-700 focus:ring-gray-500';
        }

        $this->showRoleModal = true;
    }

    public function executeRoleChange()
    {
        $userId = $this->confirmRoleUserId;
        $role = $this->confirmRoleNewRole;

        if (!auth()->user()->isAdmin()) {
            $this->dispatch('notify', type: 'error', message: "Only admins can change roles.");
            $this->showRoleModal = false;
            return;
        }

        $user = User::find($userId);
        if ($user) {
            $user->role = $role;
            $user->save();

            if ($role === 'admin') {
                Mail::send('emails.admin', ['user' => $user], function ($message) use ($user) {
                    $message->to($user->email)->subject('You are now an Admin!');
                });
            } elseif ($role === 'moderator') {
                Mail::send('emails.moderator', ['user' => $user], function ($message) use ($user) {
                    $message->to($user->email)->subject('You are now a Moderator!');
                });
            }

            $roleName = ucfirst($role);
            $this->dispatch('notify', type: 'success', message: "{$user->name} is now a {$roleName}.");
        }

        $this->showRoleModal = false;
        $this->reset(['confirmRoleUserId', 'confirmRoleNewRole', 'confirmRoleName', 'confirmRoleText', 'confirmRoleBtnText', 'confirmRoleBtnClass']);
    }

    public function confirmAction($userId, $action)
    {
        $user = User::find($userId);
        if (!$user) return;

        if (auth()->user()->isModerator() && $user->isStaff()) {
            $this->dispatch('notify', type: 'error', message: "You cannot perform actions on other staff members.");
            return;
        }

        $this->confirmActionUserId = $userId;
        $this->confirmActionType = $action;
        $this->confirmActionName = $user->name;

        if ($action === 'approved') {
            $this->confirmActionText = "Are you sure you want to approve {$user->name}? They will receive an email notification.";
            $this->confirmActionBtnText = 'Approve User';
            $this->confirmActionBtnClass = 'bg-green-600 hover:bg-green-700 focus:ring-green-500';
        } elseif ($action === 'rejected') {
            $this->confirmActionText = "Are you sure you want to reject {$user->name}'s application? They will receive an email notification.";
            $this->confirmActionBtnText = 'Reject User';
            $this->confirmActionBtnClass = 'bg-red-600 hover:bg-red-700 focus:ring-red-500';
        } elseif ($action === 'banned') {
            $this->confirmActionText = "Are you sure you want to suspend {$user->name}? They will receive an email notification.";
            $this->confirmActionBtnText = 'Suspend User';
            $this->confirmActionBtnClass = 'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500';
        } elseif ($action === 'delete') {
            $this->confirmActionText = "Are you sure you want to DELETE {$user->name}? This action cannot be undone.";
            $this->confirmActionBtnText = 'Delete User';
            $this->confirmActionBtnClass = 'bg-red-600 hover:bg-red-700 focus:ring-red-500';
        }

        $this->showConfirmModal = true;
    }

    public function executeConfirmedAction()
    {
        $userId = $this->confirmActionUserId;
        $action = $this->confirmActionType;

        if ($action === 'delete') {
            $this->deleteUser($userId);
        } else {
            $this->setUserStatus($userId, $action);
        }

        $this->showConfirmModal = false;
        $this->reset(['confirmActionUserId', 'confirmActionType', 'confirmActionName', 'confirmActionText', 'confirmActionBtnText', 'confirmActionBtnClass']);
    }

    public function setUserStatus($userId, $status)
    {
        $user = User::find($userId);
        if (!$user) return;

        if (auth()->user()->isModerator() && $user->isStaff()) {
            $this->dispatch('notify', type: 'error', message: "You cannot manage other staff members.");
            return;
        }

        $previousStatus = $user->status;
        $user->status = $status;
        $user->save();

        if ($status === 'approved') {
            if ($previousStatus === 'banned') {
                Mail::send('emails.reactivated', ['user' => $user], function ($message) use ($user) {
                    $message->to($user->email)->subject('Your Forum Account is Reactivated');
                });
            } else {
                Mail::send('emails.approved', ['user' => $user], function ($message) use ($user) {
                    $message->to($user->email)->subject('Your Forum Account is Approved!');
                });
            }
        } elseif ($status === 'rejected') {
            Mail::send('emails.rejected', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)->subject('Application Update');
            });
        } elseif ($status === 'banned') {
            Mail::send('emails.suspended', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)->subject('Account Suspended');
            });
        }

        $messages = [
            'approved' => "{$user->name} has been approved.",
            'rejected' => "{$user->name} has been rejected.",
            'banned' => "{$user->name} has been suspended.",
        ];

        $this->dispatch('notify', type: $status === 'banned' ? 'error' : 'success', message: $messages[$status] ?? "Status updated.");
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if (!$user) return;

        if (auth()->user()->isModerator() && $user->isStaff()) {
            $this->dispatch('notify', type: 'error', message: "You cannot delete other staff members.");
            return;
        }

        $name = $user->name;
        $user->delete();
        $this->dispatch('notify', type: 'error', message: "{$name} has been deleted.");
    }

    public function render(): mixed
    {
        $users = User::where('id', '!=', auth()->id())
            ->where('status', '!=', 'unverified') // <-- ADD THIS LINE
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        return view('livewire.admin.members', compact('users'));
    }
}; ?>

@section('page_title', 'Members Management')

<div class="space-y-6">
    <!-- Controls & Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:max-w-xs">
            <input type="text" wire:model.live="search" placeholder="Search by name or email..." class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-gray-50 border border-transparent focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-200 transition outline-none text-sm">
            <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>

        <div class="flex items-center space-x-3 w-full md:w-auto justify-between md:justify-end">
            <div id="filter-container" class="flex space-x-1 bg-gray-50 p-1 rounded-full border border-gray-100 relative">
                <div id="filter-slider" wire:ignore class="absolute top-1 bottom-1 rounded-full bg-white shadow-sm transition-all duration-300 ease-out"></div>
                <button data-status="all" wire:click="set('statusFilter', 'all')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $statusFilter === 'all' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">All</button>
                <button data-status="pending" wire:click="set('statusFilter', 'pending')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $statusFilter === 'pending' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Pending</button>
                <button data-status="approved" wire:click="set('statusFilter', 'approved')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $statusFilter === 'approved' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Approved</button>
                <button data-status="banned" wire:click="set('statusFilter', 'banned')" class="relative z-10 px-4 py-1.5 text-sm font-medium rounded-full transition-colors {{ $statusFilter === 'banned' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-900' }}">Suspended</button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('name')" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center"> <span>Member</span> </div>
                        </th>
                        <th wire:click="sortBy('city')" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center"> <span>Location</span> </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                        <th wire:click="sortBy('status')" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center"> <span>Status</span> </div>
                        </th>
                        <th wire:click="sortBy('created_at')" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition">
                            <div class="flex items-center"> <span>Joined</span> </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50 transition {{ $user->status == 'banned' ? 'opacity-75' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->profile_photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo_path))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover mr-3 flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-sm mr-3">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><a href="{{ route('admin.members.show', $user) }}" wire:navigate class="hover:text-primary-600 transition">{{ $user->name }}</a></div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->city }}, {{ $user->postcode }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(auth()->user()->isAdmin())
                                    <select wire:change="prepareRoleChange({{ $user->id }}, $event.target.value)" class="text-xs border-gray-200 rounded-md bg-gray-50 focus:ring-primary-500 focus:border-primary-500 outline-none py-1 pl-2 pr-8">
                                        <option value="user" @selected($user->role === 'user' || is_null($user->role))>User</option>
                                        <option value="moderator" @selected($user->role === 'moderator')>Moderator</option>
                                        <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                    </select>
                                @else
                                    <span class="text-xs font-medium capitalize px-2 py-1 rounded-md
                                        @if($user->role === 'admin') bg-purple-100 text-purple-700
                                        @elseif($user->role === 'moderator') bg-blue-100 text-blue-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $user->role ?? 'user' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->status == 'approved')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-700">Active</span>
                                @elseif($user->status == 'pending')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-700">Pending</span>
                                @elseif($user->status == 'banned')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-700">Suspended</span>
                                @elseif($user->status == 'rejected')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">Rejected</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">{{ ucfirst($user->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('F Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    @if(auth()->user()->isAdmin() || !$user->isStaff())
                                        @if($user->status == 'pending')
                                            <button wire:click="confirmAction({{ $user->id }}, 'approved')" class="inline-flex items-center px-2.5 py-1.5 border border-green-200 text-green-700 bg-green-50 rounded-md text-xs font-medium hover:bg-green-100 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Approve
                                            </button>
                                            <button wire:click="confirmAction({{ $user->id }}, 'rejected')" class="inline-flex items-center px-2.5 py-1.5 border border-red-200 text-red-700 bg-red-50 rounded-md text-xs font-medium hover:bg-red-100 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg> Reject
                                            </button>
                                        @elseif($user->status == 'approved')
                                            <button wire:click="confirmAction({{ $user->id }}, 'banned')" class="inline-flex items-center px-2.5 py-1.5 border border-orange-200 text-orange-700 bg-orange-50 rounded-md text-xs font-medium hover:bg-orange-100 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg> Suspend
                                            </button>
                                        @elseif($user->status == 'banned')
                                            <button wire:click="confirmAction({{ $user->id }}, 'approved')" class="inline-flex items-center px-2.5 py-1.5 border border-primary-200 text-primary-700 bg-primary-50 rounded-md text-xs font-medium hover:bg-primary-100 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Reactivate
                                            </button>
                                            <button wire:click="confirmAction({{ $user->id }}, 'delete')" class="inline-flex items-center px-2.5 py-1.5 border border-red-200 text-red-700 bg-red-50 rounded-md text-xs font-medium hover:bg-red-100 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg> Delete
                                            </button>
                                        @elseif($user->status == 'rejected')
                                            <button wire:click="confirmAction({{ $user->id }}, 'approved')" class="inline-flex items-center px-2.5 py-1.5 border border-green-200 text-green-700 bg-green-50 rounded-md text-xs font-medium hover:bg-green-100 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Approve
                                            </button>
                                            <button wire:click="confirmAction({{ $user->id }}, 'delete')" class="inline-flex items-center px-2.5 py-1.5 border border-red-200 text-red-700 bg-red-50 rounded-md text-xs font-medium hover:bg-red-100 transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg> Delete
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 italic">No actions</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">No members found matching your criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
        @endif
    </div>

    <!-- Action Confirmation Modal -->
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showConfirmModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Confirm Action</h3>
                                <div class="mt-2"><p class="text-sm text-gray-500">{{ $confirmActionText }}</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="executeConfirmedAction" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition {{ $confirmActionBtnClass }}">
                            <svg wire:loading.remove wire:target="executeConfirmedAction" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <svg wire:loading wire:target="executeConfirmedAction" class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span wire:loading.remove wire:target="executeConfirmedAction">{{ $confirmActionBtnText }}</span>
                            <span wire:loading wire:target="executeConfirmedAction">Processing...</span>
                        </button>
                        <button type="button" wire:click="$set('showConfirmModal', false)" wire:loading.attr="disabled" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Role Confirmation Modal -->
    @if ($showRoleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showRoleModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Change Role</h3>
                                <div class="mt-2"><p class="text-sm text-gray-500">{{ $confirmRoleText }}</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="executeRoleChange" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition {{ $confirmRoleBtnClass }}">
                            <svg wire:loading.remove wire:target="executeRoleChange" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <svg wire:loading wire:target="executeRoleChange" class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span wire:loading.remove wire:target="executeRoleChange">{{ $confirmRoleBtnText }}</span>
                            <span wire:loading wire:target="executeRoleChange">Processing...</span>
                        </button>
                        <button type="button" wire:click="$set('showRoleModal', false)" wire:loading.attr="disabled" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        function alignFilterSlider() {
            const slider = document.getElementById('filter-slider');
            if (!slider) return;
            const activeBtn = document.querySelector('#filter-container button.text-gray-900');
            if (activeBtn) {
                slider.style.left = activeBtn.offsetLeft + 'px';
                slider.style.width = activeBtn.offsetWidth + 'px';
            }
        }
        alignFilterSlider();
        window.addEventListener('resize', alignFilterSlider);
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#filter-container button');
            if (btn) {
                const slider = document.getElementById('filter-slider');
                if (slider) { slider.style.left = btn.offsetLeft + 'px'; slider.style.width = btn.offsetWidth + 'px'; }
            }
        });
        document.addEventListener('livewire:navigated', alignFilterSlider);
    </script>
</div>
