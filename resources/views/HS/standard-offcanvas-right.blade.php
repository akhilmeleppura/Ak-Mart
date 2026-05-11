@php
    $isSupreme = auth()->user()?->is_supreme_admin == 1;
@endphp

<!-- Offcanvas to add new user -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header border-bottom">
        <h5 id="offcanvasRightLabel" class="offcanvas-title">Add User</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
        <form class="add-new-user pt-0" id="addNewUserForm">
            <input type="hidden" name="id" id="user_id">

            @foreach ($formConfig['fields'] as $field => $type)
                <div class="mb-6">
                    <label class="form-label"
                        for="add-user-{{ $field }}">{{ $formConfig['labels'][$field] }}</label>

                    @if ($type === 'text' || $type === 'email')
                        <input type="{{ $type }}" class="form-control" id="add-user-{{ $field }}"
                            placeholder="{{ $formConfig['labels'][$field] }}" name="{{ $field }}"
                            value="{{ old($field) }}" />
                    @elseif ($type === 'select2' && $field === 'country')
                        <select id="{{ $field }}" class="select2 form-select" name="{{ $field }}">
                            <option value="">Select</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}" {{ old($field) === $country ? 'selected' : '' }}>
                                    {{ $country }}</option>
                            @endforeach
                        </select>
                    @elseif ($type === 'select' && $field === 'role')
                        <select id="{{ $field }}" class="form-select" name="{{ $field }}">
                            <option value="">Select a role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old($field) == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                </div>
            @endforeach

            <!-- Password Fields -->
            <div class="mb-6">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Enter Password" />
            </div>

            <div class="mb-6">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="Confirm Password" />
            </div>

            {{-- 
            @if ($isSupreme)
                <div class="mb-6">
                    <label class="form-label" for="is_super_admin">Is Super Admin?</label>
                    <select id="is_super_admin" name="is_super_admin" class="form-select">
                        <option value="0" {{ old('is_super_admin') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('is_super_admin') == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            @endif
            --}}

            <!-- Buttons -->
            <button type="submit" class="btn btn-primary me-3 data-submit">Submit</button>
            <button type="reset" class="btn btn-label-danger" data-bs-dismiss="offcanvas">Cancel</button>
        </form>
    </div>
</div>
