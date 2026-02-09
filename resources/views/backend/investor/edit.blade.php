@extends('backend.master')

@section('header_css')
    <style>
        .password-toggle {
            position: relative;
        }
        .password-toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
        .password-toggle-icon:hover {
            color: #333;
        }
    </style>
@endsection

@section('page_title')
    Investor Management
@endsection
@section('page_heading')
    Edit Investor
@endsection

@section('content')
    <div id="investorApp">
        <div class="row">
            <div class="col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-3">Edit Investor</h4>
                            <a href="{{ route('ViewAllInvestor') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>

                        <form @submit.prevent="submitForm" id="investorForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name <span class="text-danger">*</span></label>
                                        <input type="text" v-model="form.name" id="name" name="name" 
                                            class="form-control" placeholder="Enter Name" required>
                                        <span v-if="errors.name" class="text-danger">@{{ errors.name[0] }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" v-model="form.email" id="email" name="email" 
                                            class="form-control" placeholder="Enter Email">
                                        <span v-if="errors.email" class="text-danger">@{{ errors.email[0] }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone <span class="text-danger">*</span></label>
                                        <input type="text" v-model="form.phone" id="phone" name="phone" 
                                            class="form-control" placeholder="Enter Phone" required>
                                        <span v-if="errors.phone" class="text-danger">@{{ errors.phone[0] }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea v-model="form.address" id="address" name="address" 
                                            class="form-control" rows="3" placeholder="Enter Address"></textarea>
                                        <span v-if="errors.address" class="text-danger">@{{ errors.address[0] }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Invest Start Date <span class="text-danger">*</span></label>
                                        <input type="date" v-model="form.start_date" id="start_date" name="start_date" 
                                            class="form-control" required>
                                        <span v-if="errors.start_date" class="text-danger">@{{ errors.start_date[0] }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profit_ratio">Profit Ratio (%) <span class="text-danger">*</span></label>
                                        <input type="number" v-model="form.profit_ratio" id="profit_ratio" name="profit_ratio" 
                                            class="form-control" min="0" max="100" step="0.01" placeholder="0.00" required>
                                        <small class="form-text text-muted">Enter profit ratio between 0% and 100%</small>
                                        <span v-if="errors.profit_ratio" class="text-danger">@{{ errors.profit_ratio[0] }}</span>
                                    </div>
                                </div>

                                <div class="col-md-12" v-pre>
                                    <div class="form-group">
                                        @php
                                            $imageUrl = $investor->image ? (str_replace(env('FILE_URL') . '/', '', $investor->image)) : '';
                                            $fullImageUrl = $investor->image ? env('FILE_URL') . '/' . $imageUrl : '';
                                        @endphp
                                        @include('backend.components.image_upload_v2', [
                                            'inputName' => 'image',
                                            'label' => 'Image',
                                            'width' => 200,
                                            'height' => 200,
                                            'maxWidth' => '200px',
                                            'previewHeight' => '200px',
                                            'required' => false,
                                            'value' => $imageUrl,
                                            'imageUrl' => $fullImageUrl
                                        ])
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group password-toggle">
                                        <label for="password">Password <small>(Leave blank to keep current password)</small></label>
                                        <input :type="showPassword ? 'text' : 'password'" v-model="form.password" 
                                            id="password" name="password" class="form-control" 
                                            placeholder="Enter New Password">
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                                            class="password-toggle-icon" @click="showPassword = !showPassword"></i>
                                        <span v-if="errors.password" class="text-danger">@{{ errors.password[0] }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group password-toggle">
                                        <label for="confirm_password">Confirm Password <span v-if="hasPassword" class="text-danger">*</span></label>
                                        <input :type="showConfirmPassword ? 'text' : 'password'" 
                                            v-model="form.confirm_password" id="confirm_password" name="confirm_password" 
                                            class="form-control" :required="hasPassword" placeholder="Confirm Password">
                                        <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
                                            class="password-toggle-icon" @click="showConfirmPassword = !showConfirmPassword"></i>
                                        <span v-if="errors.confirm_password" class="text-danger">@{{ errors.confirm_password[0] }}</span>
                                        <span v-if="hasPassword && passwordMismatch" class="text-danger">Passwords do not match</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="form-group text-center">
                                        <button type="submit" :disabled="loading || (hasPassword && passwordMismatch)" class="btn btn-primary">
                                            <span v-if="loading">Updating...</span>
                                            <span v-else><i class="fas fa-save"></i> Update Investor</span>
                                        </button>
                                        <a href="{{ route('ViewAllInvestor') }}" class="btn btn-danger">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        new Vue({
            el: '#investorApp',
            data: {
                form: {
                    name: '{{ $investor->name }}',
                    email: '{{ $investor->email ?? "" }}',
                    phone: '{{ $investor->phone }}',
                    address: '{{ $investor->address ?? "" }}',
                    start_date: '{{ $investorRule->start_date ?? "" }}',
                    profit_ratio: '{{ $investorRule->profit_ratio ?? "" }}',
                    image: '',
                    password: '',
                    confirm_password: ''
                },
                errors: {},
                loading: false,
                showPassword: false,
                showConfirmPassword: false
            },
            computed: {
                passwordMismatch() {
                    // Only check mismatch if password is provided
                    if (!this.form.password) {
                        return false;
                    }
                    return this.form.password && this.form.confirm_password && 
                           this.form.password !== this.form.confirm_password;
                },
                hasPassword() {
                    return !!this.form.password;
                }
            },
            methods: {
                submitForm() {
                    // Only validate password if it's provided
                    if (this.hasPassword && this.passwordMismatch) {
                        return;
                    }

                    this.loading = true;
                    this.errors = {};

                    // Get image value from hidden input
                    const imageInput = document.getElementById('image_id');
                    if (imageInput) {
                        this.form.image = imageInput.value;
                    }

                    const formData = new FormData();
                    formData.append('name', this.form.name);
                    formData.append('email', this.form.email || '');
                    formData.append('phone', this.form.phone);
                    formData.append('address', this.form.address || '');
                    formData.append('start_date', this.form.start_date);
                    formData.append('profit_ratio', this.form.profit_ratio);
                    // Only append password fields if password is provided
                    if (this.form.password && this.form.password.trim() !== '') {
                        formData.append('password', this.form.password);
                        formData.append('confirm_password', this.form.confirm_password || '');
                    }
                    if (this.form.image) {
                        formData.append('image', this.form.image);
                    }

                    axios.post('{{ route("UpdateInvestor", $investor->id) }}', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.data && response.data.success) {
                            toastr.success(response.data.message || 'Investor updated successfully!');
                            setTimeout(() => {
                                window.location.href = '{{ route("ViewAllInvestor") }}';
                            }, 1000);
                        } else {
                            toastr.error(response.data?.message || 'Error updating investor');
                            this.loading = false;
                        }
                    })
                    .catch(error => {
                        if (error.response && error.response.status === 302) {
                            // Handle redirect response
                            toastr.success('Investor updated successfully!');
                            setTimeout(() => {
                                window.location.href = '{{ route("ViewAllInvestor") }}';
                            }, 1000);
                        } else if (error.response && error.response.data && error.response.data.errors) {
                            this.errors = error.response.data.errors;
                            this.loading = false;
                        } else {
                            toastr.error(error.response?.data?.message || 'Error updating investor');
                            this.loading = false;
                        }
                    })
                    .finally(() => {
                        // Only set loading to false if not redirecting
                        if (!error || (error.response && error.response.status !== 302)) {
                            this.loading = false;
                        }
                    });
                }
            }
        });
    </script>
@endpush

