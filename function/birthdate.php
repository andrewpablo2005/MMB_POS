<?php

function mmb_birthdate_to_storage($value): ?int
{
	$input = trim((string) $value);
	if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $input)) {
		return null;
	}

	$date = DateTimeImmutable::createFromFormat('!Y-m-d', $input);
	$errors = DateTimeImmutable::getLastErrors();
	if (
		!$date
		|| ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
		|| $date->format('Y-m-d') !== $input
		|| $date > new DateTimeImmutable('today')
	) {
		return null;
	}

	return (int) $date->format('Ymd');
}

function mmb_birthdate_to_input($value): string
{
	$stored = (string) $value;
	if (!preg_match('/^\d{8}$/', $stored)) {
		return '';
	}

	$date = DateTimeImmutable::createFromFormat('!Ymd', $stored);
	$errors = DateTimeImmutable::getLastErrors();
	if (
		!$date
		|| ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
		|| $date->format('Ymd') !== $stored
		|| $date > new DateTimeImmutable('today')
	) {
		return '';
	}

	return $date->format('Y-m-d');
}

function mmb_birthdate_for_display($value): string
{
	$inputDate = mmb_birthdate_to_input($value);
	if ($inputDate !== '') {
		return $inputDate;
	}

	$legacy = trim((string) $value);
	if ($legacy === '' || $legacy === '0') {
		return 'Not recorded';
	}
	if (preg_match('/^\d{4}$/', $legacy)) {
		return $legacy . ' (year only; legacy)';
	}
	if (preg_match('/^\d{1,3}$/', $legacy)) {
		return $legacy . ' (legacy age)';
	}

	return 'Legacy value: ' . $legacy;
}

function mmb_age_from_birthdate($value): ?int
{
	$inputDate = mmb_birthdate_to_input($value);
	if ($inputDate === '') {
		return null;
	}

	$birthDate = new DateTimeImmutable($inputDate);
	$today = new DateTimeImmutable('today');

	return $birthDate->diff($today)->y;
}
