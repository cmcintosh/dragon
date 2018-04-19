import { isUndefined, isElement, defaults } from 'underscore';

const deps = [
  require('utils'),
  require('keymaps'),
  require('undo_manager'),
  require('storage_manager'),
  require('device_manager'),
  require('parser'),
  require('selector_manager'),
  require('modal_dialog'),
  require('code_manager'),
  require('panels'),
  require('rich_text_editor'),
  require('style_manager'),
  require('asset_manager'),
  require('css_composer'),
  require('trait_manager'),
  require('dom_components'),
  require('navigator'),
  require('canvas'),
  require('commands'),
  require('block_manager')
];

const Backbone = require('backbone');
let timedInterval;

require('utils/extender')({
  Backbone: Backbone,
  $: Backbone.$
});

const $ = Backbone.$;

module.exports = Backbone.Model.extend({
  defaults: {
    clipboard: null,
    designerMode: false,
    selectedComponent: null,
    previousModel: null,
    changesCount: 0,
    storables: [],
    modules: [],
    toLoad: [],
    opened: {},
    device: ''
  },

  initialize(c = {}) {
    this.config = c;
    this.set('Config', c);
    this.set('modules', []);
    this.set('toLoad', []);
    this.set('storables', []);

    if (c.el && c.fromElement) this.config.components = c.el.innerHTML;

    // Load modules
    deps.forEach(name => this.loadModule(name));
    this.on('change:selectedComponent', this.componentSelected, this);
    this.on('change:changesCount', this.updateChanges, this);
  },

  /**
   * Get configurations
   * @param  {string} [prop] Property name
   * @return {any} Returns the configuration object or
   *  the value of the specified property
   */
  getConfig(prop) {
    const config = this.config;
    return isUndefined(prop) ? config : config[prop];
  },

  /**
   * Should be called after all modules and plugins are loaded
   * @param {Function} clb
   * @private
   */
  loadOnStart(clb = null) {
    const sm = this.get('StorageManager');

    // Generally, with `onLoad`, the module will try to load the data from
    // its configurations
    this.get('toLoad').forEach(module => {
      module.onLoad();
    });

    // Stuff to do post load
    const postLoad = () => {
      const modules = this.get('modules');
      modules.forEach(module => module.postLoad && module.postLoad(this));
      clb && clb();
    };

    if (sm && sm.canAutoload()) {
      this.load(postLoad);
    } else {
      postLoad();
    }
  },

  /**
   * Set the alert before unload in case it's requested
   * and there are unsaved changes
   * @private
   */
  updateChanges() {
    const stm = this.get('StorageManager');
    const changes = this.get('changesCount');

    if (this.config.noticeOnUnload && changes) {
      window.onbeforeunload = e => 1;
    } else {
      window.onbeforeunload = null;
    }

    if (stm.isAutosave() && changes >= stm.getStepsBeforeSave()) {
      this.store();
    }
  },

  /**
   * Load generic module
   * @param {String} moduleName Module name
   * @return {this}
   * @private
   */
  loadModule(moduleName) {
    var c = this.config;
    var Mod = new moduleName();
    var name = Mod.name.charAt(0).toLowerCase() + Mod.name.slice(1);
    var cfg = c[name] || c[Mod.name] || {};
    cfg.pStylePrefix = c.pStylePrefix || '';

    // Check if module is storable
    var sm = this.get('StorageManager');

    if (Mod.storageKey && Mod.store && Mod.load && sm) {
      cfg.stm = sm;
      var storables = this.get('storables');
      storables.push(Mod);
      this.set('storables', storables);
    }

    cfg.em = this;
    Mod.init({ ...cfg });

    // Bind the module to the editor model if public
    !Mod.private && this.set(Mod.name, Mod);
    Mod.onLoad && this.get('toLoad').push(Mod);
    this.get('modules').push(Mod);
    return this;
  },

  /**
   * Initialize editor model and set editor instance
   * @param {Editor} editor Editor instance
   * @return {this}
   * @private
   */
  init(editor) {
    this.set('Editor', editor);
  },

  getEditor() {
    return this.get('Editor');
  },

  /**
   * This method handles updates on the editor and tries to store them
   * if requested and if the changesCount is exceeded
   * @param  {Object} model
   * @param  {any} val  Value
   * @param  {Object} opt  Options
   * @private
   * */
  handleUpdates(model, val, opt = {}) {
    // Component has been added temporarily - do not update storage or record changes
    if (opt.temporary) {
      return;
    }

    timedInterval && clearInterval(timedInterval);
    timedInterval = setTimeout(() => {
      if (!opt.avoidStore) {
        this.set('changesCount', this.get('changesCount') + 1, opt);
      }
    }, 0);
  },

  /**
   * Callback on component selection
   * @param   {Object}   Model
   * @param   {Mixed}   New value
   * @param   {Object}   Options
   * @private
   * */
  componentSelected(model, val, options) {
    if (!this.get('selectedComponent')) {
      this.trigger('deselect-comp');
    } else {
      this.trigger('select-comp', [model, val, options]);
      this.trigger('component:selected', arguments);
    }
  },

  /**
   * Returns model of the selected component
   * @return {Component|null}
   * @private
   */
  getSelected() {
    return this.get('selectedComponent');
  },

  /**
   * Select a component
   * @param  {Component|HTMLElement} el Component to select
   * @param  {Object} [opts={}] Options, optional
   * @private
   */
  setSelected(el, opts = {}) {
    let model = el;
    isElement(el) && (model = $(el).data('model'));
    if (model && !model.get('selectable')) return;
    opts.forceChange && this.set('selectedComponent', '');
    this.set('selectedComponent', model, opts);
  },

  /**
   * Set components inside editor's canvas. This method overrides actual components
   * @param {Object|string} components HTML string or components model
   * @return {this}
   * @private
   */
  setComponents(components) {
    return this.get('DomComponents').setComponents(components);
  },

  /**
   * Returns components model from the editor's canvas
   * @return {Components}
   * @private
   */
  getComponents() {
    var cmp = this.get('DomComponents');
    var cm = this.get('CodeManager');

    if (!cmp || !cm) return;

    var wrp = cmp.getComponents();
    return cm.getCode(wrp, 'json');
  },

  /**
   * Set style inside editor's canvas. This method overrides actual style
   * @param {Object|string} style CSS string or style model
   * @return {this}
   * @private
   */
  setStyle(style) {
    var rules = this.get('CssComposer').getAll();
    for (var i = 0, len = rules.length; i < len; i++) rules.pop();
    rules.add(style);
    return this;
  },

  /**
   * Returns rules/style model from the editor's canvas
   * @return {Rules}
   * @private
   */
  getStyle() {
    return this.get('CssComposer').getAll();
  },

  /**
   * Returns HTML built inside canvas
   * @return {string} HTML string
   * @private
   */
  getHtml() {
    const config = this.config;
    const exportWrapper = config.exportWrapper;
    const wrappesIsBody = config.wrappesIsBody;
    const js = config.jsInHtml ? this.getJs() : '';
    var wrp = this.get('DomComponents').getComponent();
    var html = this.get('CodeManager').getCode(wrp, 'html', {
      exportWrapper,
      wrappesIsBody
    });
    html += js ? `<script>${js}</script>` : '';
    return html;
  },

  /**
   * Returns CSS built inside canvas
   * @param {Object} [opts={}] Options
   * @return {string} CSS string
   * @private
   */
  getCss(opts = {}) {
    const config = this.config;
    const wrappesIsBody = config.wrappesIsBody;
    const avoidProt = opts.avoidProtected;
    const cssc = this.get('CssComposer');
    const wrp = this.get('DomComponents').getComponent();
    const protCss = !avoidProt ? config.protectedCss : '';

    return (
      protCss +
      this.get('CodeManager').getCode(wrp, 'css', {
        cssc,
        wrappesIsBody
      })
    );
  },

  /**
   * Returns JS of all components
   * @return {string} JS string
   * @private
   */
  getJs() {
    var wrp = this.get('DomComponents').getWrapper();
    return this.get('CodeManager')
      .getCode(wrp, 'js')
      .trim();
  },

  /**
   * Store data to the current storage
   * @param {Function} clb Callback function
   * @return {Object} Stored data
   * @private
   */
  store(clb) {
    var sm = this.get('StorageManager');
    var store = {};
    if (!sm) return;

    // Fetch what to store
    this.get('storables').forEach(m => {
      var obj = m.store(1);
      for (var el in obj) store[el] = obj[el];
    });

    sm.store(store, res => {
      clb && clb(res);
      this.set('changesCount', 0);
      this.trigger('storage:store', store);
    });

    return store;
  },

  /**
   * Load data from the current storage
   * @param {Function} clb Callback function
   * @private
   */
  load(clb = null) {
    this.getCacheLoad(1, res => {
      this.get('storables').forEach(module => module.load(res));
      clb && clb(res);
    });
  },

  /**
   * Returns cached load
   * @param {Boolean} force Force to reload
   * @param {Function} clb Callback function
   * @return {Object}
   * @private
   */
  getCacheLoad(force, clb) {
    var f = force ? 1 : 0;
    if (this.cacheLoad && !f) return this.cacheLoad;
    var sm = this.get('StorageManager');
    var load = [];

    if (!sm) return {};

    this.get('storables').forEach(m => {
      var key = m.storageKey;
      key = typeof key === 'function' ? key() : key;
      var keys = key instanceof Array ? key : [key];
      keys.forEach(k => {
        load.push(k);
      });
    });

    sm.load(load, res => {
      this.cacheLoad = res;
      clb && clb(res);
      setTimeout(() => this.trigger('storage:load', res), 0);
    });
  },

  /**
   * Returns device model by name
   * @return {Device|null}
   * @private
   */
  getDeviceModel() {
    var name = this.get('device');
    return this.get('DeviceManager').get(name);
  },

  /**
   * Run default command if setted
   * @param {Object} [opts={}] Options
   * @private
   */
  runDefault(opts = {}) {
    var command = this.get('Commands').get(this.config.defaultCommand);
    if (!command || this.defaultRunning) return;
    command.stop(this, this, opts);
    command.run(this, this, opts);
    this.defaultRunning = 1;
  },

  /**
   * Stop default command
   * @param {Object} [opts={}] Options
   * @private
   */
  stopDefault(opts = {}) {
    var command = this.get('Commands').get(this.config.defaultCommand);
    if (!command) return;
    command.stop(this, this, opts);
    this.defaultRunning = 0;
  },

  /**
   * Update canvas dimensions and refresh data useful for tools positioning
   * @private
   */
  refreshCanvas() {
    this.set('canvasOffset', this.get('Canvas').getOffset());
  },

  /**
   * Clear all selected stuf inside the window, sometimes is useful to call before
   * doing some dragging opearation
   * @param {Window} win If not passed the current one will be used
   * @private
   */
  clearSelection(win) {
    var w = win || window;
    w.getSelection().removeAllRanges();
  },

  /**
   * Get the current media text
   * @return {string}
   */
  getCurrentMedia() {
    const config = this.config;
    const device = this.getDeviceModel();
    const condition = config.mediaCondition;
    const preview = config.devicePreviewMode;
    const width = device && device.get('widthMedia');
    return device && width && !preview ? `(${condition}: ${width})` : '';
  },

  /**
   * Set/get data from the HTMLElement
   * @param  {HTMLElement} el
   * @param  {string} name Data name
   * @param  {any} value Date value
   * @return {any}
   * @private
   */
  data(el, name, value) {
    const varName = '_gjs-data';

    if (!el[varName]) {
      el[varName] = {};
    }

    if (isUndefined(value)) {
      return el[varName][name];
    } else {
      el[varName][name] = value;
    }
  }
});
