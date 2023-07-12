<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="title" class="form-label">
                {{ __('Title') }}
            </label>
            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $user?->title) }}">
            @error('title')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="first-name" class="form-label">
                {{ __('First Name') }}
                <span class="text-danger">*</span>
            </label>
            <input type="text" id="first-name" name="first_name" class="form-control" required
                   value="{{ old('first_name', $user?->first_name) }}">
            @error('first_name')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="last-name" class="form-label">
                {{ __('Last Name') }}
            </label>
            <input type="text" id="last-name" name="last_name" class="form-control" value="{{ old('last_name', $user?->last_name) }}">
            @error('last_name')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">
                {{ __('Email') }}
                <span class="text-danger">*</span>
            </label>
            <input type="email" id="email" name="email" class="form-control" required value="{{ old('email', $user?->email) }}">
            @error('email')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">
                {{ __('Phone') }}
            </label>
            <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $user?->phone) }}">
            @error('phone')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone-alt" class="form-label">
                {{ __('Phone (Alt)') }}
            </label>
            <input type="text" id="phone-alt" name="phone_alt" class="form-control" value="{{ old('phone_alt', $user?->phone_alt) }}">
            @error('phone_alt')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="gender" class="form-label">
                {{ __('Gender') }}
            </label>
            <select type="text" id="gender" name="gender" class="form-control">
                <option value=""></option>
                <option value="male" {{ old('gender', $user?->gender) === 'male' ? 'selected' : '' }}>
                    {{__('Male')}}
                </option>
                <option value="female" {{ old('gender', $user?->gender) === 'female' ? 'selected' : '' }}>
                    {{__('Female')}}
                </option>
                <option value="other" {{ old('gender', $user?->gender) === 'other' ? 'selected' : '' }}>
                    {{__('Other')}}
                </option>
            </select>
            @error('gender')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">
                {{ __('Role') }}
                <span class="text-danger">*</span>
            </label>
            <select type="text" id="role" name="role" class="form-control">
                <option value="user" {{ old('role', $user?->role) === 'user' ? 'selected' : '' }}>
                    {{__('User')}}
                </option>
                <option value="admin" {{ old('role', $user?->role) === 'admin' ? 'selected' : '' }}>
                    {{__('Admin')}}
                </option>
            </select>
            @error('role')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="street" class="form-label">
                {{ __('Street') }}
            </label>
            <input type="text" id="street" name="street" class="form-control" value="{{ old('street', $user->street) }}">
            @error('street')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="street-additional" class="form-label">
                {{ __('Street (Additional)') }}
            </label>
            <input type="text" id="street-additional" name="street_additional" class="form-control"
                   value="{{ old('street_additional', $user->street_additional) }}">
            @error('street_additional')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="city" class="form-label">
                {{ __('City') }}
            </label>
            <input type="text" id="city" name="city" class="form-control" value="{{ old('city', $user->city) }}">
            @error('city')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="postcode" class="form-label">
                {{ __('Postcode') }}
            </label>
            <input type="text" id="postcode" name="postcode" class="form-control" value="{{ old('postcode', $user->postcode) }}">
            @error('postcode')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="country" class="form-label">
                {{ __('Country') }}
            </label>
            <input type="text" id="country" name="country" class="form-control" value="{{ old('country', $user->country) }}">
            @error('country')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="birthdate" class="form-label">
                {{ __('Birthdate') }}
            </label>
            <input type="date" id="birthdate" name="birthdate" class="form-control" value="{{ old('birthdate', $user->birthdate) }}"
                   max="{{date('Y-m-d')}}">
            @error('birthdate')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">
                {{ __('Password') }}
                <span class="text-danger" {{ $user ? 'hidden' : ''  }}>*</span>
            </label>
            <input type="password" id="password" name="password" class="form-control" value="{{ old('password') }}"  @required(!$user)>
            @error('password')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password-confirmation" class="form-label">
                {{ __('Password (Repeat)') }}
                <span class="text-danger" {{ $user ? 'hidden' : ''  }}>*</span>
            </label>
            <input type="password" id="password-confirmation" name="password_confirmation" class="form-control"
                   value="{{ old('password_confirmation') }}" @required(!$user)>
            @error('password_confirmation')
            <span class="form-text text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
