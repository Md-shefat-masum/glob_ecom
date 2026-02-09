@php
    $isEdit = isset($target) && $target && $target->id;
@endphp

<div class="form-group">
    <label for="user_id">Employee <span class="text-danger">*</span></label>
    <select id="user_id" name="user_id" class="form-control" required>
        @if($isEdit && $target->user)
            <option value="{{ $target->user_id }}" selected>{{ $target->user->name }}</option>
        @endif
    </select>
    @error('user_id')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
    <label for="date">Date <span class="text-danger">*</span></label>
    <input type="date" id="date" name="date" class="form-control" value="{{ old('date', $isEdit ? $target->date->format('Y-m-d') : date('Y-m-d')) }}" required>
    @error('date')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
    <label for="target">Target <span class="text-danger">*</span></label>
    <input type="number" step="0.01" min="0" id="target" name="target" class="form-control" value="{{ old('target', $isEdit ? $target->target : 0) }}" required>
    @error('target')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="form-group">
    <label for="note">Note</label>
    <textarea id="note" name="note" class="form-control" rows="3" placeholder="Optional note">{{ old('note', $isEdit ? $target->note : '') }}</textarea>
    @error('note')<div class="text-danger">{{ $message }}</div>@enderror
</div>

@if($isEdit)
    <div class="form-group">
        <label for="completed">Completed</label>
        <input type="number" step="0.01" min="0" id="completed" name="completed" class="form-control" value="{{ old('completed', $target->completed) }}">
        @error('completed')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label for="remains">Remains</label>
        <input type="number" step="0.01" min="0" id="remains" name="remains" class="form-control" value="{{ old('remains', $target->remains) }}">
        @error('remains')<div class="text-danger">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="is_evaluated" name="is_evaluated" value="1" {{ old('is_evaluated', $target->is_evaluated) ? 'checked' : '' }}>
            <label class="custom-control-label" for="is_evaluated">Is Evaluated</label>
        </div>
    </div>
@endif
