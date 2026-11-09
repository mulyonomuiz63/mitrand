<?php
    $fieldId    = $id ?? 'password';
    $fieldName  = $name ?? $fieldId;
    $fieldLabel = $label ?? 'Kata Sandi';
    $fieldAuto  = $autocomplete ?? 'current-password';
    $fieldMin   = isset($minlength) ? (int) $minlength : 0;
?>
<div class="mb-3">
    <label class="auth-label" for="<?= esc($fieldId) ?>"><?= esc($fieldLabel) ?></label>
    <div class="pass-wrap">
        <input class="form-control" id="<?= esc($fieldId) ?>" name="<?= esc($fieldName) ?>" type="password"<?= $fieldMin > 0 ? ' minlength="' . $fieldMin . '"' : '' ?> required autocomplete="<?= esc($fieldAuto) ?>">
        <button class="pass-toggle" type="button" data-toggle-password="<?= esc($fieldId) ?>" aria-label="Tampilkan kata sandi">
            <i class="bi bi-eye"></i>
        </button>
    </div>
</div>
