// SIGNUP FUNCTION
function justValidate() {
	//let emailAvailable;

	function debounce(fn, time) {
		// Variable pour stocker l'ID du timer
		var timer;

		// La fonction renvoyée qui encapsule `fn`
		return function () {
			// Annule le timer précédent (s'il existe) pour éviter que la fonction ne soit exécutée plusieurs fois
			clearTimeout(timer);

			// Crée un nouveau timer pour appeler la fonction `fn` après `time` millisecondes
			timer = setTimeout(() => {
				// Applique la fonction `fn` avec les mêmes arguments que ceux passés à la nouvelle fonction
				fn.apply(this, arguments);
			}, time);
		};
	}

	$(function () {
		const validation = new JustValidate("#signup-form", {
			errorFieldCssClass: "error-field",
			successFieldCssClass: "success-field",
			defaultStyles: false,
			validateBeforeSubmitting: true,
			lockForm: true,
			focusInvalidField: false,
			successLabelCssClass: ["success"],
			errorLabelCssClass: ["errors"],
		});

		validation
			.addField(
				"#mail",
				[
					{
						rule: "required",
						errorMessage: "Field is required",
					},
					{
						rule: "email",
						errorMessage: "Invalid email format",
					},
                    {
						validator: function(value) {
							return function() {
								return $.getJSON("user/email_available/" + value);
							}
						},
						errorMessage: 'Mail already exists',
					},
                    
				],
				{ successMessage: "Looks good !" }
			)

			.addField(
				"#full_name",
				[
					{
						rule: "required",
						errorMessage: "Field is required",
					},
					{
						rule: "minLength",
						value: 3,
						errorMessage: "Full Name must be at least 3 characters",
					},
				],
				{ successMessage: "Looks good !" }
			)

			.addField(
				"#iban",
				[
					{
						rule: "required",
						errorMessage: "Field is required",
					},
					{
						rule: "customRegexp",
						value: /^[a-zA-Z]{2}\d{2}\s(\d{4}\s)+\d{4}$/,
						errorMessage: "Invalid IBAN format",
					},
				],
				{ successMessage: "Looks good !" }
			)

			.addField(
				"#password",
				[
					{
						rule: "required",
						errorMessage: "Field is required",
					},
					{
						rule: "minLength",
						value: 8,
						errorMessage: "Minimum 8 characters",
					},
					{
						rule: "maxLength",
						value: 16,
						errorMessage: "Maximum 16 characters",
					},
					{
						rule: "customRegexp",
						value: /[A-Z]/,
						errorMessage: "Password must contain an uppercase letter",
					},
					{
						rule: "customRegexp",
						value: /\d/,
						errorMessage: "Password must contain a digit",
					},
					{
						rule: "customRegexp",
						value: /['";:,.\/?\\-]/,
						errorMessage: "Password must contain a special character",
					},
				],
				{ successMessage: "Looks good !" }
			)

			.addField(
				"#password_confirm",
				[
					{
						rule: "required",
						errorMessage: "Field is required",
					},
					{
						validator: function (value, fields) {
							if (fields["#password"] && fields["#password"].elem) {
								const repeatPasswordValue = fields["#password"].elem.value;
								return value === repeatPasswordValue;
							}
							return true;
						},
						errorMessage: "Passwords should be the same",
					},
				],
				{ successMessage: "Looks good !" }
			)

			.onValidate(debounce(async function(event) {
				pseudoAvailable = await $.getJSON("user/email_available/" + $("#mail").val());
				if (!pseudoAvailable)
					this.showErrors({ '#mail': 'Mail already exists' });
			}, 300))

			.onSuccess(function (event) {
				if (pseudoAvailable) event.target.submit();
			});

		$("input:text:first").focus();
	});
}

// LOGIN

function JVLogin() {
	function debounce(fn, time) {
		var timer;

		return function () {
			clearTimeout(timer);

			timer = setTimeout(() => {
				fn.apply(this, arguments);
			}, time);
		};
	}

	async function checkUserExists(email) {
		const userExists = await $.getJSON("user/validateEmail " + email);
		return userExists;
	}

	$(function () {
		const validation = new JustValidate("#login-form", {
			errorFieldCssClass: "error-field",
			successFieldCssClass: "success-field",
			defaultStyles: false,
			validateBeforeSubmitting: true,
			lockForm: true,
			focusInvalidField: false,
			successLabelCssClass: ["success"],
			errorLabelCssClass: ["errors"],
		});

		validation
			.addField(
				"#mail",
				[
					{
						rule: "required",
						errorMessage: "Field is required",
					},
					{
						rule: "email",
						errorMessage: "Invalid email format",
					},
				],
				{ successMessage: "Looks good !" }
			)

			.addField(
				"#password",
				[
					{
						rule: "required",
						errorMessage: "Field is required",
					},
				],
				{ successMessage: "Looks good !" }
			);

		$("#mail").on(
			"input",
			debounce(async function () {
				const userEmail = $("#mail").val();
				const userExists = await checkUserExists(userEmail);
				if (!userExists) {
					validation.showErrors({ "#mail": "User not found" });
				} else {
					validation.hideErrors("#mail");
				}
			}, 300)
		);

		validation
			.onValidate(
				debounce(async function (event) {
					const userEmail = $("#mail").val();
					const password = $("#password").val();

					const loginErrors = await $.post("user/validate_login", {
						mail: userEmail,
						password: password,
					});
					if (loginErrors.length > 0) {
						for (const error of loginErrors) {
							if (error.includes("Wrong password")) {
								validation.showErrors({ "#password": error });
							} else if (error.includes("Can't find a user")) {
								validation.showErrors({ "#mail": error });
							}
						}
					}
				}, 300)
			)

			.onSuccess(function (event) {
				event.target.submit();
			});

		$("input:text:first").focus();
	});
}

//ADD OPERATION
function JVAddOperation() {
	function debounce(fn, time) {
		let timer;
		return function () {
			const args = arguments;
			clearTimeout(timer);
			timer = setTimeout(() => {
				fn.apply(this, args);
			}, time);
		};
	}

	function glowInput(selector, glowColor) {
		const input = document.querySelector(selector);
		input.style.borderColor = glowColor;
		input.style.boxShadow = `0 0 5px ${glowColor}`;
	}

	$(function () {
		const validation = new JustValidate("#add-exp-form", {
			errorFieldCssClass: "error-field",
			successFieldCssClass: "success-field",
			defaultStyles: false,
			validateBeforeSubmitting: true,
			lockForm: true,
			focusInvalidField: false,
			successLabelCssClass: ["success"],
			errorLabelCssClass: ["errors"],
			saveTemplateCheckbox: "#save",
		});

		validation
			.addField("#title", [
				{
					rule: "required",
					errorMessage: "Le titre est obligatoire",
				},
				{
					rule: "minLength",
					value: 3,
					errorMessage: "Title must be at least 3 characters",
				},
				{
					rule: "maxLength",
					value: 256,
					errorMessage: "Title must be at max 256 characters",
				},
			])

			.addField("#amount", [
				{
					rule: "required",
					errorMessage: "Le montant est obligatoire",
				},
				{
					rule: "minNumber",
					value: 0.01,
					errorMessage:
						"Le montant doit être supérieur ou égal à un centime d'euro",
				},
			]);

		validation.addField("#operation_date", [
			{
				rule: "required",
				errorMessage: "La date est obligatoire",
				onFail: () => glowInput("#operation_date", "red"),
				onSuccess: () => glowInput("#operation_date", "limegreen"),
			},
			{
				validator: function (value) {
					const today = new Date();
					const inputDate = new Date(value);
					const isDateInFuture = inputDate > today;
					return !isDateInFuture;
				},
				errorMessage: "La date ne peut pas être dans le futur",
				onFail: () => glowInput("#operation_date", "red"),
				onSuccess: () => glowInput("#operation_date", "limegreen"),
			},
		]);

		validation.addField("#savename", [
			{
				rule: "minLength",
				value: 3,
				errorMessage: "Template name must be at least 3 characters",
				validator: debounce(function (value) {
                    const tricId = document.querySelector("#tricId").value;
                    console.log("enter check");
					// Make an AJAX call to check if the name is available
					fetch(`templates/validateTemplateNameForIt3/${value}/${tricId}`)
						.then((response) => response.json())
						.then((data) => {
							if (!data.isAvailable) {
								// If the name is not available, show an error.
								throw new Error(
									"This name is already taken. Please choose a different name."
								);
							}
						})
						.catch((error) => {
							console.error("There was an error!", error);
						});
				}, 300),
			},
			{
				rule: "maxLength",
				value: 256,
				errorMessage: "Template name must be at max 256 characters",
			},
			{
				rule: "minLength",
				value: 3,
				errorMessage: "Template name must be at min 3 characters",
			},
		]);

		validation.onValidate().onSuccess(function () {
			document.querySelector("#add-exp-form").submit();
		});

		$("input:text:first").focus();
	});

	function validateCheckboxes() {
		const checkboxes = document.querySelectorAll('[id$="_userCheckbox"]');
		const weightInputs = document.querySelectorAll("#userWeight");
		const amountInputs = document.querySelectorAll('[id$="_amount"]');
		const saveCheckbox = document.querySelector("#save");
		const repartitionTemplate = document.querySelector("#repartitionTemplate");
		const selectedOption =
			repartitionTemplate.options[repartitionTemplate.selectedIndex].value;

		// If the 'save' checkbox is not checked or an option from the dropdown is selected, return true.
		if (!saveCheckbox.checked || selectedOption !== "option-default") {
			return true;
		}

		let atLeastOneChecked = false;
		let sumOfWeights = 0;

		checkboxes.forEach((checkbox, index) => {
			if (checkbox.checked) {
				atLeastOneChecked = true;
				sumOfWeights += parseInt(weightInputs[index].value);
			}
		});

		if (!atLeastOneChecked || sumOfWeights <= 0) {
			// Show custom error message and apply glowing red border
			weightInputs.forEach((input) => {
				input.style.borderColor = "red";
				input.style.boxShadow = "0 0 5px red";
			});
			amountInputs.forEach((input) => {
				input.style.borderColor = "red";
				input.style.boxShadow = "0 0 5px red";
			});
			return false;
		} else {
			// Hide custom error message and apply glowing green border
			weightInputs.forEach((input) => {
				input.style.borderColor = "limegreen";
				input.style.boxShadow = "0 0 5px limegreen";
			});
			amountInputs.forEach((input) => {
				input.style.borderColor = "limegreen";
				input.style.boxShadow = "0 0 5px limegreen";
			});
			return true;
		}
	}

	// Add event listeners to checkboxes and weight inputs
	const checkboxes = document.querySelectorAll('[id$="_userCheckbox"]');
	const weightInputs = document.querySelectorAll("#userWeight");

	checkboxes.forEach((checkbox) => {
		checkbox.addEventListener("change", () => {
			validateCheckboxes();
		});
	});

	weightInputs.forEach((weightInput) => {
		weightInput.addEventListener("input", () => {
			validateCheckboxes();
		});
	});

	// Add custom validation to the form submission
	const form = document.getElementById("add-exp-form");
	if (form) {
		form.addEventListener("submit", (event) => {
			const saveCheckbox = document.querySelector("#save");

			// If the 'save' checkbox is not checked, submit the form.
			if (!saveCheckbox.checked) {
				return;
			}

			// If the 'save' checkbox is checked, validate the checkboxes.
			if (!validateCheckboxes()) {
				event.preventDefault();
				// You can display an error message here if needed
			} else {
				// Form is valid and will be submitted
			}
		});
	}
}

//EDIT PROFILE
function JVEditProfile() {
	function debounce(func, wait) {
		let timeout;
		return function () {
			clearTimeout(timeout);
			timeout = setTimeout(() => func.apply(this, arguments), wait);
		};
	}

	$(function () {
		const validation = new JustValidate("#edit_profile", {
			errorFieldCssClass: "error-field",
			successFieldCssClass: "success-field",
			defaultStyles: false,
			validateBeforeSubmitting: true,
			lockForm: true,
			focusInvalidField: false,
			successLabelCssClass: ["success"],
			errorLabelCssClass: ["errors"],
		});

		validation
			.addField(
				"#mail",
				[
					{
						rule: "required",
						errorMessage: "Field is required",
					},
					{
						rule: "email",
						errorMessage: "Invalid email format",
					},
				],
				{ successMessage: "Looks good !" }
			)

			.addField(
				"#fullName",
				[
					{
						rule: "required",
						errorMessage: "Field is required",
					},
					{
						rule: "minLength",
						value: 3,
						errorMessage: "Full Name must be at least 3 characters",
					},
				],
				{ successMessage: "Looks good !" }
			)

			.addField(
				"#iban",
				[
					{
						rule: "customRegexp",
						value: /^[BE]+\d\d(\s([0-9]+\s)+)\d\d\d\d$/,
						errorMessage: "Invalid IBAN format",
					},
				],
				{ successMessage: "Looks good !" }
			);

		const debouncedValidation = debounce(() => validation.validate(), 300);
		$("#edit_profile").on("input", debouncedValidation);
	});
}

//CHANGE PASSWORD
function JVChangePassword() {
	$(document).ready(function () {
		function debounce(fn, time) {
			var timer;

			return function () {
				clearTimeout(timer);

				timer = setTimeout(() => {
					fn.apply(this, arguments);
				}, time);
			};
		}

		const chpassForm = new JustValidate(".chpass-form", {
			errorFieldCssClass: "error-field",
			successFieldCssClass: "success-field",
			defaultStyles: false,
			validateBeforeSubmitting: true,
			lockForm: true,
			focusInvalidField: false,
			successLabelCssClass: ["success"],
			errorLabelCssClass: ["errors"],
		});

		async function checkCurrentPassword(password) {
			const userId = $("#userId").val();
			try {
				const user = JSON.parse($("input[name='user']").val());

				const response = await $.post("user/checkUserPass/", {
					currentPassword: password,
					user: user,
				});
				const result = JSON.parse(response);
				return result.isPasswordCorrect;
			} catch (error) {
				console.error("Error checking current password:", error);
				return false;
			}
		}

		chpassForm.addField("#currentPassword", [
			{
				rule: "required",
				errorMessage: "Field is required",
			},
			{
				validator: async function (value) {
					const isPasswordCorrect = await checkCurrentPassword(value, user);
					return isPasswordCorrect;
				},

				errorMessage: "Current password is incorrect",
			},
			{
				rule: "minLength",
				value: 8,
				errorMessage: "Minimum 8 characters",
			},
			{
				rule: "maxLength",
				value: 16,
				errorMessage: "Maximum 16 characters",
			},
			{
				rule: "customRegexp",
				value: /[A-Z]/,
				errorMessage: "Password must contain an uppercase letter",
			},
			{
				rule: "customRegexp",
				value: /\d/,
				errorMessage: "Password must contain a digit",
			},
			{
				rule: "customRegexp",
				value: /['";:,.\/?\\-]/,
				errorMessage: "Password must contain a special character",
			},
		]);

		chpassForm.addField("#newPassword", [
			{
				rule: "required",
				errorMessage: "Field is required",
			},
			{
				rule: "minLength",
				value: 8,
				errorMessage: "Minimum 8 characters",
			},
			{
				rule: "maxLength",
				value: 16,
				errorMessage: "Maximum 16 characters",
			},
			{
				rule: "customRegexp",
				value: /[A-Z]/,
				errorMessage: "Password must contain an uppercase letter",
			},
			{
				rule: "customRegexp",
				value: /\d/,
				errorMessage: "Password must contain a digit",
			},
			{
				rule: "customRegexp",
				value: /['";:,.\/?\\-]/,
				errorMessage: "Password must contain a special character",
			},
		]);

		chpassForm.addField("#confirmPassword", [
			{
				rule: "required",
				errorMessage: "Field is required",
			},
			{
				validator: function (value, fields) {
					if (fields["#newPassword"] && fields["#newPassword"].elem) {
						const newPasswordValue = fields["#newPassword"].elem.value;
						return value === newPasswordValue;
					}
					return true;
				},
				errorMessage: "Passwords should be the same",
			},
		]);

		chpassForm.onSuccess(function (event) {
			event.target.submit();
		});

		$("input:text:first").focus();
	});
}

//ADD TRICOUNT

function JVAddTricount() {
	function debounce(func, wait) {
		let timeout;
		return function () {
			clearTimeout(timeout);
			timeout = setTimeout(() => func.apply(this, arguments), wait);
		};
	}

	// Implement this function to check if the title exists on the server-side
	function validateTitle(title, callback) {
		// Perform server-side validation and call the callback with a boolean result
	}

	$(function () {
		const validation = new JustValidate("#addTricount", {
			errorFieldCssClass: "error-field",
			successFieldCssClass: "success-field",
			defaultStyles: false,
			validateBeforeSubmitting: true,
			lockForm: true,
			focusInvalidField: false,
			successLabelCssClass: ["success"],
			errorLabelCssClass: ["errors"],
		});

		validation
			.addField(
				"#text4b",
				[
					{
						rule: "minLength",
						value: 3,
						errorMessage: "Title must have at least 3 characters",
						customValidation: (field, value, errorMessage, resolve) => {
							validateTitle(value, function (isTitleValid) {
								if (!isTitleValid) {
									resolve({
										isValid: false,
										errorMessage: "Title must be unique for the creator",
									});
								} else {
									resolve({ isValid: true });
								}
							});
						},
					},
				],
				{ successMessage: "Looks good !" }
			)

			.addField(
				"#textarea4b",
				[
					{
						rule: "minLength",
						value: 3,
						errorMessage:
							"Description must have at least 3 characters if provided",
						isOptional: true,
					},
				],
				{ successMessage: "Looks good !" }
			);

		const debouncedValidation = debounce(() => validation.validate(), 300);
		$("#addTricount").on("input", debouncedValidation);
	});
}

//EDIT TRICOUNT
function JVEditTricount() {
	function debounce(func, wait) {
		let timeout;
		return function () {
			clearTimeout(timeout);
			timeout = setTimeout(() => func.apply(this, arguments), wait);
		};
	}

	// Implement this function to check if the title exists on the server-side
	function validateTitle(title, callback) {
		// Perform server-side validation and call the callback with a boolean result
	}

	$(function () {
		const validation = new JustValidate("#updateTricount", {
			errorFieldCssClass: "error-field",
			successFieldCssClass: "success-field",
			defaultStyles: false,
			validateBeforeSubmitting: true,
			lockForm: true,
			focusInvalidField: false,
			successLabelCssClass: ["success"],
			errorLabelCssClass: ["errors"],
		});

		validation
			.addField(
				"#title",
				[
					{
						rule: "minLength",
						value: 3,
						errorMessage: "Title must have at least 3 characters",
						customValidation: (field, value, errorMessage, resolve) => {
							validateTitle(value, function (isTitleValid) {
								if (!isTitleValid) {
									resolve({
										isValid: false,
										errorMessage: "Title must be unique for the creator",
									});
								} else {
									resolve({ isValid: true });
								}
							});
						},
					},
				],
				{ successMessage: "Looks good !" }
			)

			.addField(
				"#description",
				[
					{
						rule: "minLength",
						value: 3,
						errorMessage:
							"Description must have at least 3 characters if provided",
						isOptional: true,
					},
				],
				{ successMessage: "Looks good !" }
			);

		const debouncedValidation = debounce(() => validation.validate(), 300);
		$("#updateTricount").on("input", debouncedValidation);
	});
}

function JVEditTemplate() {
	function debounce(fn, time) {
		var timer;

		return function () {
			const args = arguments;
			clearTimeout(timer);

			timer = setTimeout(() => {
				fn.apply(this, args);
			}, time);
		};
	}

	function setGlowingBorder(elements, color) {
		elements.forEach((element) => {
			const parentDiv = element.closest(".edit_template_items");
			const userTextInput = parentDiv.querySelector('input[name="user"]');

			element.style.boxShadow =
				color === "green" ? "0 0 5px 1px limegreen" : "0 0 5px 1px red";
			userTextInput.style.borderColor = color === "green" ? "limegreen" : "red";
			userTextInput.style.boxShadow =
				color === "green" ? "0 0 5px limegreen" : "0 0 5px red";
		});
	}

	function validateCheckboxes() {
		const checkboxes = document.querySelectorAll(".check");

		let atLeastOneChecked = false;

		checkboxes.forEach((checkbox) => {
			if (checkbox.checked) {
				atLeastOneChecked = true;
			}
		});

		if (!atLeastOneChecked) {
			setGlowingBorder(checkboxes, "red");
			return false;
		} else {
			setGlowingBorder(checkboxes, "green");
			return true;
		}
	}

	const form = document.getElementById("edit_template_form");
	form.addEventListener("submit", (event) => {
		if (!validateCheckboxes()) {
			event.preventDefault();
		}
	});

	const checkboxes = document.querySelectorAll(".check");
	checkboxes.forEach((checkbox) => {
		checkbox.addEventListener("change", () => {
			validateCheckboxes();
		});
	});

	$(function () {
		const validation = new JustValidate("#edit_template_form", {
			errorFieldCssClass: "error-field",
			successFieldCssClass: "success-field",
			defaultStyles: false,
			validateBeforeSubmitting: true,
			lockForm: true,
			focusInvalidField: false,
			successLabelCssClass: ["success"],
			errorLabelCssClass: ["errors"],
		});

		validation.addField("#template_title", [
			{
				rule: "required",
				errorMessage: "Title is required",
			},
			{
				rule: "minLength",
				value: 3,
				errorMessage: "Title must be at least 3 characters",
			},
			{
				rule: "maxLength",
				value: 256,
				errorMessage: "Title must be at max 256 characters",
			},
		]);

		const checkboxes = document.querySelectorAll(".check");
		checkboxes.forEach((checkbox) => {
			checkbox.addEventListener("change", () => {
				validateCheckboxes();
			});
		});

		const form = document.getElementById("edit_template_form");
		form.addEventListener("submit", (event) => {
			if (!validateCheckboxes()) {
				event.preventDefault();
			}
		});
	});
}
