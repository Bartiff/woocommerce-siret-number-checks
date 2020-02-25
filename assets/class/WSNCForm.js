import { FetchRequest } from './FetchRequest.js'

export class WSNCForm {

    /**
     * Constructor
     * @param [HTMLSelectElement] form
     */
    constructor(form, wpadmin = false) {
        this.form = form
        this.wpadmin = wpadmin
        this.siretLength = 14
        this.inputCompanyField = this.form.querySelector('#wsnc_company_field')
        this.inputSiretField = this.form.querySelector('#wsnc_siret_field')
        this.inputCompany = this.form.querySelector('#wsnc_company')
        this.inputSiret = this.form.querySelector('#wsnc_siret')
        if (this.inputCompany.value.length === 0) {
            this.inputCompanyField.style.display = 'none'
        }
        if (WsncOptions.wsnc_check_siret_number !== 'on') {
            this.inputCompany.setAttribute('disabled', 'disabled')
            this.inputCompanyField.style.display = 'none'
        }
        this.inputSiret.addEventListener('input', this.onChange.bind(this))
    }

    /**
     * Triggering when the value of a select is changed
     * @param [Event] e
     */
    onChange(e) {
        e.target.value = e.target.value.replace(' ', '')
        if (e.target.value.length >= this.siretLength) {
            this.loadReqRequest(e.target.value)
        } else {
            this.inputSiret.classList.remove('wsnc-error')
            this.inputSiret.classList.remove('wsnc-success')
        }
    }

    /**
     * Load Fetch Request
     * @param [String] siret
     */
    async loadReqRequest(siret) {
        if (WsncOptions.wsnc_check_siret_number === 'on') {
            this.inputSiret.setAttribute('readonly', 'readonly')
            let span = document.createElement('span')
            span.classList.add('wsnc-icon-check')
            span.innerHTML = 'SIRET check running...'
            this.inputSiretField.firstChild.appendChild(span)
            const req = new FetchRequest('GET', '/wp-admin/admin-ajax.php?action=wsnc_fetch_siret&siret=' + siret)
            let results = await req.run()
            if (results.success) {
                if (results.array_return.length !== 0) {
                    let infosSiret = results.array_return[0]
                    this.inputCompany.value = infosSiret.L1_NORMALISEE
                    this.inputCompanyField.style.display = 'block'
                    if (!this.wpadmin) {
                        this.inputSiret.setAttribute('readonly', 'readonly')
                    } else {
                        this.inputSiret.removeAttribute('readonly')
                    }
                    this.inputSiret.classList.remove('wsnc-error')
                    this.inputSiret.classList.add('wsnc-success')
                    this.inputCompany.classList.add('wsnc-success')
                    this.inputSiretField.firstChild.removeChild(span)
                    console.info('SIRET valid !')
                } else {
                    this.inputCompanyField.style.display = 'none'
                    this.inputCompany.classList.remove('wsnc-success')
                    this.inputSiret.classList.remove('wsnc-success')
                    this.inputSiret.classList.add('wsnc-error')
                    this.inputSiretField.firstChild.removeChild(span)
                    console.warn('SIRET not valid')
                    this.inputCompany.value = ''
                    this.inputSiret.removeAttribute('readonly')
                }
            } else {
                console.warn('Verify API connecion')
            }
        } else {
            console.info('Automatic verify not enable')
        }
    }
}