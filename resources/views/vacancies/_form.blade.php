{{-- Formulário compartilhado entre criação e edição de vagas. --}}
<div class="form-grid">
    <div class="field field--full">
        <label for="title">Título <span class="required">*</span></label>
        <input type="text" id="title" name="title" maxlength="150" required
               value="{{ old('title', $vacancy->title) }}" @class(['is-invalid' => $errors->has('title')])>
        @include('partials.field-error', ['field' => 'title'])
    </div>

    <div class="field">
        <label for="company">Empresa <span class="required">*</span></label>
        <input type="text" id="company" name="company" maxlength="150" required
               value="{{ old('company', $vacancy->company) }}" @class(['is-invalid' => $errors->has('company')])>
        @include('partials.field-error', ['field' => 'company'])
    </div>

    <div class="field">
        <label for="location">Local</label>
        <input type="text" id="location" name="location" maxlength="150" placeholder="Ex.: Teresina - PI ou Remoto"
               value="{{ old('location', $vacancy->location) }}" @class(['is-invalid' => $errors->has('location')])>
        @include('partials.field-error', ['field' => 'location'])
    </div>

    <div class="field">
        <label for="contract_type">Tipo de contratação <span class="required">*</span></label>
        <select id="contract_type" name="contract_type" required @class(['is-invalid' => $errors->has('contract_type')])>
            <option value="">Selecione...</option>
            @foreach ($contractTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('contract_type', $vacancy->contract_type?->value) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @include('partials.field-error', ['field' => 'contract_type'])
    </div>

    <div class="field">
        <label for="salary">Salário (R$)</label>
        <input type="number" id="salary" name="salary" step="0.01" min="0" placeholder="Deixe vazio para 'A combinar'"
               value="{{ old('salary', $vacancy->salary) }}" @class(['is-invalid' => $errors->has('salary')])>
        @include('partials.field-error', ['field' => 'salary'])
    </div>

    <div class="field field--full">
        <label for="description">Descrição <span class="required">*</span></label>
        <textarea id="description" name="description" rows="6" maxlength="5000" required
                  @class(['is-invalid' => $errors->has('description')])>{{ old('description', $vacancy->description) }}</textarea>
        @include('partials.field-error', ['field' => 'description'])
    </div>

    <div class="field field--full">
        <input type="hidden" name="is_paused" value="0">
        <label class="checkbox">
            <input type="checkbox" name="is_paused" value="1" @checked(old('is_paused', $vacancy->is_paused))>
            Vaga pausada (não aceita novas inscrições)
        </label>
    </div>
</div>
