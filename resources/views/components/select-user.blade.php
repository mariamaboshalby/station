<div class="mb-3">
    <label for="user_id" class="form-label fw-bold">اختر الموظف</label>
    <select name="user_id" id="user_id" class="form-select" required>
        <option value="">-- اختر الموظف --</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }}</option>
        @endforeach
    </select>
</div>
