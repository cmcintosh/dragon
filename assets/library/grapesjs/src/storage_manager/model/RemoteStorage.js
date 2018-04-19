import fetch from 'utils/fetch';
import { isUndefined } from 'underscore';

module.exports = require('backbone').Model.extend({
  fetch,

  defaults: {
    urlStore: '',
    urlLoad: '',
    params: {},
    beforeSend() {},
    onComplete() {},
    contentTypeJson: false
  },

  /**
   * Triggered before the request is started
   * @private
   */
  onStart() {
    const em = this.get('em');
    const before = this.get('beforeSend');
    before && before();
    em && em.trigger('storage:start');
  },

  /**
   * Triggered on request error
   * @param  {Object} err Error
   * @private
   */
  onError(err) {
    const em = this.get('em');
    console.error(err);
    em && em.trigger('storage:error', err);
    this.onEnd(err);
  },

  /**
   * Triggered after the request is ended
   * @param  {Object|string} res End result
   * @private
   */
  onEnd(res) {
    const em = this.get('em');
    em && em.trigger('storage:end', res);
  },

  /**
   * Triggered on request response
   * @param  {string} text Response text
   * @private
   */
  onResponse(text, clb) {
    const em = this.get('em');
    const complete = this.get('onComplete');
    const typeJson = this.get('contentTypeJson');
    const parsable = text && typeof text === 'string';
    const res = typeJson && parsable ? JSON.parse(text) : text;
    complete && complete(res);
    clb && clb(res);
    em && em.trigger('storage:response', res);
    this.onEnd(text);
  },

  store(data, clb) {
    const body = {};

    for (let key in data) {
      body[key] = data[key];
    }

    this.request(this.get('urlStore'), { body }, clb);
  },

  load(keys, clb) {
    this.request(this.get('urlLoad'), { method: 'get' }, clb);
  },

  /**
   * Execute remote request
   * @param  {string} url Url
   * @param  {Object} [opts={}] Options
   * @param  {[type]} [clb=null] Callback
   * @private
   */
  request(url, opts = {}, clb = null) {
    const typeJson = this.get('contentTypeJson');
    const headers = this.get('headers') || {};
    const params = this.get('params');
    const reqHead = 'X-Requested-With';
    const typeHead = 'Content-Type';
    const bodyObj = opts.body || {};
    let fetchOptions;
    let body;

    for (let param in params) {
      bodyObj[param] = params[param];
    }

    if (isUndefined(headers[reqHead])) {
      headers[reqHead] = 'XMLHttpRequest';
    }

    // With `fetch`, have to send FormData without any 'Content-Type'
    // https://stackoverflow.com/questions/39280438/fetch-missing-boundary-in-multipart-form-data-post

    if (isUndefined(headers[typeHead]) && typeJson) {
      headers[typeHead] = 'application/json; charset=utf-8';
    }

    if (typeJson) {
      body = JSON.stringify(bodyObj);
    } else {
      body = new FormData();

      for (let bodyKey in bodyObj) {
        body.append(bodyKey, bodyObj[bodyKey]);
      }
    }
    fetchOptions = {
      method: opts.method || 'post',
      credentials: 'include',
      headers
    };

    // Body should only be included on POST method
    if (fetchOptions.method === 'post') {
      fetchOptions.body = body;
    }

    this.onStart();
    this.fetch(url, fetchOptions)
      .then(
        res =>
          ((res.status / 200) | 0) == 1
            ? res.text()
            : res.text().then(text => Promise.reject(text))
      )
      .then(text => this.onResponse(text, clb))
      .catch(err => this.onError(err));
  }
});
