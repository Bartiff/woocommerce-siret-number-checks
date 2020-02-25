export class FetchRequest {

    /**
     * @param
     */
    constructor(method, url) {
        this.method = method
        this.url = url
        this.createRequest()
        this.datas
    }

    createRequest() {
        this.request = new Request(this.url, {
            method: this.method,
            headers: {
                'Content-Type': 'application/json'
            }
        })
    }

    async run() {
        try {
            let response = await fetch(this.request)
            if (response.ok) {
                this.datas = await response.json()
            } else {
                console.error('Retour du serveur : ', response.status)
            }
        } catch (e) {
            console.error(e)
        }
        return this.datas
    }

}