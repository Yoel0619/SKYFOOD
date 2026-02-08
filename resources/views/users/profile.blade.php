@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>My Profile</h1>
    </div>
    
    <div class="profile-container">
        <!-- Profile Info Card -->
        <div class="card">
            <div class="card-header">
                <h2>Profile Information</h2>
            </div>
            
            <form id="profileForm" action="{{ route('profile.update') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name">Full Name *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="name" 
                        name="name" 
                        value="{{ $user->name }}"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        value="{{ $user->email }}"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="phone" 
                        name="phone" 
                        value="{{ $user->phone }}"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="address">Address *</label>
                    <textarea 
                        class="form-control" 
                        id="address" 
                        name="address" 
                        rows="3"
                        required
                    >{{ $user->address }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    💾 Update Profile
                </button>
            </form>
        </div>
        
        <!-- Change Password Card -->
        <div class="card">
            <div class="card-header">
                <h2>Change Password</h2>
            </div>
            
            <form id="passwordForm" action="{{ route('profile.update') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="current_password" 
                        name="current_password"
                        placeholder="Enter current password"
                    >
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="new_password" 
                        name="new_password"
                        placeholder="Minimum 8 characters"
                    >
                </div>
                
                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="new_password_confirmation" 
                        name="new_password_confirmation"
                        placeholder="Re-enter new password"
                    >
                </div>
                
                <button type="submit" class="btn btn-warning">
                    🔒 Change Password
                </button>
            </form>
        </div>
        
        <!-- Account Stats -->
        <div class="card">
            <div class="card-header">
                <h2>Account Statistics</h2>
            </div>
            
            <div class="stats-list">
                <div class="stat-item">
                    <div class="stat-icon">📦</div>
                    <div class="stat-details">
                        <h3>{{ $user->orders->count() }}</h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-icon">✅</div>
                    <div class="stat-details">
                        <h3>{{ $user->orders->where('status', 'completed')->count() }}</h3>
                        <p>Completed Orders</p>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-icon">💰</div>
                    <div class="stat-details">
                        <h3>TZS {{ number_format($user->orders->where('status', 'completed')->sum('total_amount'), 0) }}</h3>
                        <p>Total Spent</p>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-icon">📅</div>
                    <div class="stat-details">
                        <h3>{{ $user->created_at->format('M d, Y') }}</h3>
                        <p>Member Since</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .profile-container {
        max-width: 800px;
        margin: 2rem auto;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    
    .stats-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--light-color);
        border-radius: 8px;
    }
    
    .stat-icon {
        font-size: 2rem;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 12px;
    }
    
    .stat-details h3 {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
        color: var(--primary-color);
    }
    
    .stat-details p {
        color: #636e72;
        font-size: 0.875rem;
    }
    
    @media (max-width: 576px) {
        .stats-list {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
handleFormSubmit('profileForm', (data) => {
    // Don't redirect after profile update
    setTimeout(() => location.reload(), 1500);
});

handleFormSubmit('passwordForm', (data) => {
    // Clear password fields
    document.getElementById('current_password').value = '';
    document.getElementById('new_password').value = '';
    document.getElementById('new_password_confirmation').value = '';
});
</script>
@endpush