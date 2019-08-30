import axios from 'axios';

class ApiClient {
    constructor(client) {
        this.client = client;
    }


    get(url, conf = {}) {
        console.log(`API_CLIENT.GET(${url})`);
        return this.client.get(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    delete(url, conf = {}) {
        return this.client.delete(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    head(url, conf = {}) {
        return this.client.head(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    options(url, conf = {}) {
        return this.client.options(url, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    post(url, data = {}, conf = {}) {
        return this.client.post(url, data, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    put(url, data = {}, conf = {}) {
        return this.client.put(url, data, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }

    patch(url, data = {}, conf = {}) {
        return this.client.patch(url, data, conf)
            .then(response => Promise.resolve(response))
            .catch(error => Promise.reject(error));
    }
}


export default {
    data() {
        return {
            client: null,
        }
    },
    created() {
        this.client = new ApiClient(this.getClient(this, `/api/braceyourself/${_(this.model).kebabCase()}s`));

    },
    methods:{
        getClient(vue, baseUrl = null){

            const options = {
                baseURL: baseUrl
            };

            if (this.$store.getters['authenticated']) {
                options.headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.$store.getters.get('csrfToken')
                }
            }

            const client = axios.create(options);

            client.interceptors.request.use(
                requestConfig => {
                    if (requestConfig.url) {
                        requestConfig.url = `${requestConfig.url}`;
                    }
                    return Promise.resolve(requestConfig)
                },
                requestError => {
                    return Promise.reject(requestError);
                }
            );

            client.interceptors.response.use(
                response => response,
                error => {
                    return Promise.reject(error);
                }
            );

            return client;
        }
    }

}
