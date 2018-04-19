const CssRuleView = require('./CssRuleView');
const CssGroupRuleView = require('./CssGroupRuleView');
const $ = Backbone.$;

module.exports = require('backbone').View.extend({
  initialize(o) {
    const config = o.config || {};
    this.atRules = {};
    this.config = config;
    this.em = config.em;
    this.pfx = config.stylePrefix || '';
    this.className = this.pfx + 'rules';
    const coll = this.collection;
    this.listenTo(coll, 'add', this.addTo);
    this.listenTo(coll, 'reset', this.render);
  },

  /**
   * Add to collection
   * @param {Object} model
   * @private
   * */
  addTo(model) {
    this.addToCollection(model);
  },

  /**
   * Add new object to collection
   * @param {Object} model
   * @param {Object} fragmentEl
   * @return {Object}
   * @private
   * */
  addToCollection(model, fragmentEl) {
    var fragment = fragmentEl || null;
    var viewObject = CssRuleView;
    var config = this.config;
    let rendered, view;
    const opts = { model, config };

    // I have to render keyframes of the same name together
    // Unfortunately at the moment I didn't find the way of appending them
    // if not staticly, via appendData
    if (model.get('atRuleType') === 'keyframes') {
      const atRule = model.getAtRule();
      let atRuleEl = this.atRules[atRule];

      if (!atRuleEl) {
        const styleEl = document.createElement('style');
        atRuleEl = document.createTextNode('');
        styleEl.appendChild(document.createTextNode(`${atRule}{`));
        styleEl.appendChild(atRuleEl);
        styleEl.appendChild(document.createTextNode(`}`));
        this.atRules[atRule] = atRuleEl;
        rendered = styleEl;
      }

      view = new CssGroupRuleView(opts);
      atRuleEl.appendData(view.render().el.textContent);
    } else {
      view = new CssRuleView(opts);
      rendered = view.render().el;
    }

    const mediaWidth = this.getMediaWidth(model.get('mediaText'));
    const styleBlockId = `#${this.pfx}rules-${mediaWidth}`;

    if (rendered) {
      if (fragment) {
        fragment.querySelector(styleBlockId).appendChild(rendered);
      } else {
        let $stylesContainer = this.$el.find(styleBlockId);
        $stylesContainer.append(rendered);
      }
    }

    return rendered;
  },

  getMediaWidth(mediaText) {
    return (
      mediaText &&
      mediaText
        .replace(`(${this.em.getConfig('mediaCondition')}: `, '')
        .replace(')', '')
    );
  },

  render() {
    this.atRules = {};
    const $el = this.$el;
    const frag = document.createDocumentFragment();
    $el.empty();

    // Create devices related DOM structure
    const pfx = this.pfx;
    this.em
      .get('DeviceManager')
      .getAll()
      .map(model => model.get('widthMedia'))
      .sort(
        (left, right) =>
          ((right && right.replace('px', '')) || Number.MAX_VALUE) -
          ((left && left.replace('px', '')) || Number.MAX_VALUE)
      )
      .forEach(widthMedia => {
        const blockId = pfx + 'rules-' + widthMedia;
        $(`<div id="${blockId}"></div>`).appendTo(frag);
      });

    this.collection.each(model => this.addToCollection(model, frag));
    $el.append(frag);
    $el.attr('class', this.className);
    return this;
  }
});
