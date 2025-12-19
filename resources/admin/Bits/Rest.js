const request = function (method, route, data = {}) {
    const url = `${window.fluentAddonVars.rest.url}/${route}`;

    const headers = {'X-WP-Nonce': window.fluentAddonVars.rest.nonce};

    if (['PUT', 'PATCH', 'DELETE'].indexOf(method.toUpperCase()) !== -1) {
        headers['X-HTTP-Method-Override'] = method;
        method = 'POST';
    }
    if(method == "POST") {
        window['request_is_pending'] = true
    }

    data.query_timestamp = Date.now();

    return new Promise((resolve, reject) => {
        window.jQuery.ajax({
            url: url,
            type: method,
            data: data,
            headers: headers
        })
            .then(response => resolve(response))
            .fail(errors => reject(errors.responseJSON))
            .then(()=> window['request_is_pending'] = false);
    });
}

export default {
    get(route, data = {}) {
        return request('GET', route, data);
    },
    post(route, data = {}) {
        return request('POST', route, data);
    },
    delete(route, data = {}) {
        return request('DELETE', route, data);
    },
    put(route, data = {}) {
        return request('PUT', route, data);
    },
    patch(route, data = {}) {
        return request('PATCH', route, data);
    },
    uploadFile(route, data = {}) {
        const url = `${window.fluentAddonVars.rest.url}/${route}`;
        const headers = {'X-WP-Nonce': window.fluentAddonVars.rest.nonce};

        return new Promise((resolve, reject) => {
            window.jQuery.ajax({
                url: url,
                type: 'POST',
                data: data,
                headers: headers,
                processData: false,
                contentType: false
            })
                .then(response => resolve(response))
                .fail(errors => reject(errors.responseJSON));
        });
    }
};

jQuery(document).ajaxSuccess((event, xhr, settings) => {
    const nonce = xhr.getResponseHeader('X-WP-Nonce');
    if (nonce) {
        window.fluentAddonVars.rest_nonce = nonce;
    }
});
