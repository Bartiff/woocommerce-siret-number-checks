import '../css/styles-public.scss'
import { WSNCForm } from '../../class/WSNCForm.js'

window.addEventListener('DOMContentLoaded', (e) => {
	if (document.querySelector('#wsnc_siret_field')) {
		let form = document.querySelector('#wsnc_siret_field').closest('form')
		new WSNCForm(form)
	}
});
