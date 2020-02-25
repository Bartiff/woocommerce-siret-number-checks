import '../css/styles-admin.scss'
import { WSNCForm } from '../../class/WSNCForm.js'

var checkSiretInput = function (input, element) {
	if (input.checked) {
		element.removeAttribute('style')
		element.previousElementSibling.removeAttribute('style')
		element.nextElementSibling.removeAttribute('style')
	} else {
		element.style.display = 'none'
		element.previousElementSibling.style.display = 'none'
		element.nextElementSibling.style.display = 'none'
	}
}

window.addEventListener('DOMContentLoaded', (e) => {
	if (document.querySelector('#wsnc_siret_field')) {
		let form = document.querySelector('#wsnc_siret_field').closest('form')
		new WSNCForm(form, true)
	}
	
	let inputWhoHide = document.querySelector('#wsnc-check-siret-number')
	let elToHide = document.querySelector('#api-numero-de-siret')
	if (inputWhoHide) {
		checkSiretInput(inputWhoHide, elToHide)
		inputWhoHide.addEventListener('click', (e) => {
			checkSiretInput(inputWhoHide, elToHide)
		})
	}
});