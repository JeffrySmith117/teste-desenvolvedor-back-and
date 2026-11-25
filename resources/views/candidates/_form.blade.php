{{-- Formulário compartilhado entre criação e edição de candidatos. --}}
<div class="form-grid">
    <div class="field">
        <label for="name">Nome <span class="required">*</span></label>
        <input type="text" id="name" name="name" maxlength="150" required
               value="{{ old('name', $candidate->name) }}" @class(['is-invalid' => $errors->has('name')])>
        @include('partials.field-error', ['field' => 'name'])
    </div>

    <div class="field">
        <label for="email">E-mail <span class="required">*</span></label>
        <input type="email" id="email" name="email" maxlength="150" required
               value="{{ old('email', $candidate->email) }}" @class(['is-invalid' => $errors->has('email')])>
        @include('partials.field-error', ['field' => 'email'])
    </div>

    <div class="field">
        <label for="phone">Telefone</label>
        <input type="tel" id="phone" name="phone" maxlength="20" placeholder="(86) 99999-9999"
               value="{{ old('phone', $candidate->phone) }}" @class(['is-invalid' => $errors->has('phone')])>
        @include('partials.field-error', ['field' => 'phone'])
    </div>

    <div class="field">
        <label for="birth_date">Data de nascimento</label>
        <input type="date" id="birth_date" name="birth_date"
               value="{{ old('birth_date', $candidate->birth_date?->format('Y-m-d')) }}" @class(['is-invalid' => $errors->has('birth_date')])>
        @include('partials.field-error', ['field' => 'birth_date'])
    </div>

    <div class="field field--full">
        <label for="linkedin_url">LinkedIn</label>
        <input type="url" id="linkedin_url" name="linkedin_url" maxlength="255" placeholder="https://www.linkedin.com/in/seu-perfil"
               value="{{ old('linkedin_url', $candidate->linkedin_url) }}" @class(['is-invalid' => $errors->has('linkedin_url')])>
        @include('partials.field-error', ['field' => 'linkedin_url'])
    </div>

    <div class="field field--full">
        <label for="summary">Resumo profissional</label>
        <textarea id="summary" name="summary" rows="5" maxlength="5000"
                  @class(['is-invalid' => $errors->has('summary')])>{{ old('summary', $candidate->summary) }}</textarea>
        @include('partials.field-error', ['field' => 'summary'])
    </div>
</div>