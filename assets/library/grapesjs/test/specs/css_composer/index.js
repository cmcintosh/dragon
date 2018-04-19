const Models = require('./model/CssModels');
const CssRuleView = require('./view/CssRuleView');
const CssRulesView = require('./view/CssRulesView');
const CssComposer = require('css_composer');
const e2e = require('./e2e/CssComposer');
const utils = require('./../test_utils.js');
const Editor = require('editor/model/Editor');

describe('Css Composer', () => {
  describe('Main', () => {
    var obj;
    var em;
    var config;
    var storagMock = utils.storageMock();
    var editorModel = {
      getCss() {
        return 'testCss';
      },
      getCacheLoad() {
        return storagMock.load();
      }
    };

    var setSmConfig = () => {
      config.stm = storagMock;
      config.stm.getConfig = () => ({
        storeCss: 1,
        storeStyles: 1
      });
    };
    var setEm = () => {
      config.em = editorModel;
    };

    beforeEach(() => {
      em = new Editor({});
      config = { em };
      obj = new CssComposer().init(config);
    });

    afterEach(() => {
      obj = null;
    });

    it('Object exists', () => {
      expect(CssComposer).toExist();
    });

    it('storageKey returns array', () => {
      expect(obj.storageKey() instanceof Array).toEqual(true);
    });

    it('storageKey returns correct composition', () => {
      setSmConfig();
      expect(obj.storageKey()).toEqual(['css', 'styles']);
    });

    it('Store data', () => {
      setSmConfig();
      setEm();
      var expected = { css: 'testCss', styles: '[]' };
      expect(obj.store(1)).toEqual(expected);
    });

    it('Rules are empty', () => {
      expect(obj.getAll().length).toEqual(0);
    });

    it('Create new rule with correct selectors', () => {
      var sel = new obj.Selectors();
      var s1 = sel.add({ name: 'test1' });
      var rule = obj.add(sel.models);
      expect(rule.get('selectors').at(0)).toEqual(s1);
    });

    it('Create new rule correctly', () => {
      var sel = new obj.Selectors();
      var s1 = sel.add({ name: 'test1' });
      var rule = obj.add(sel.models, 'state1', 'width1');
      expect(rule.get('state')).toEqual('state1');
      expect(rule.get('mediaText')).toEqual('width1');
    });

    it('Add rule to collection', () => {
      var sel = new obj.Selectors([{ name: 'test1' }]);
      var rule = obj.add(sel.models);
      expect(obj.getAll().length).toEqual(1);
      expect(
        obj
          .getAll()
          .at(0)
          .get('selectors')
          .at(0)
          .get('name')
      ).toEqual('test1');
    });

    it('Returns correct rule with the same selector', () => {
      var sel = new obj.Selectors([{ name: 'test1' }]);
      var rule1 = obj.add(sel.models);
      var rule2 = obj.get(sel.models);
      expect(rule1).toEqual(rule2);
    });

    it('Returns correct rule with the same selectors', () => {
      var sel1 = new obj.Selectors([{ name: 'test1' }]);
      var rule1 = obj.add(sel1.models);

      var sel2 = new obj.Selectors([{ name: 'test21' }, { name: 'test22' }]);
      var rule2 = obj.add(sel2.models);

      var rule3 = obj.get(sel2.models);
      expect(rule3).toEqual(rule2);
    });

    it('Do not create multiple rules with the same name selectors', () => {
      var sel1 = new obj.Selectors([{ name: 'test21' }, { name: 'test22' }]);
      var rule1 = obj.add(sel1.models);

      var sel2 = new obj.Selectors([{ name: 'test22' }, { name: 'test21' }]);
      var rule2 = obj.add(sel2.models);
      expect(rule2).toEqual(rule1);
    });

    it("Don't duplicate rules", () => {
      var sel = new obj.Selectors([]);
      var s1 = sel.add({ name: 'test1' });
      var s2 = sel.add({ name: 'test2' });
      var s3 = sel.add({ name: 'test3' });

      var rule1 = obj.add([s1, s3]);
      var rule2 = obj.add([s3, s1]);

      expect(rule2).toEqual(rule1);
    });

    it('Returns correct rule with the same mixed selectors', () => {
      var sel = new obj.Selectors([]);
      var s1 = sel.add({ name: 'test1' });
      var s2 = sel.add({ name: 'test2' });
      var s3 = sel.add({ name: 'test3' });
      var rule1 = obj.add([s1, s3]);
      var rule2 = obj.get([s3, s1]);
      expect(rule2).toEqual(rule1);
    });

    it('Returns correct rule with the same selectors and state', () => {
      var sel = new obj.Selectors([]);
      var s1 = sel.add({ name: 'test1' });
      var s2 = sel.add({ name: 'test2' });
      var s3 = sel.add({ name: 'test3' });
      var rule1 = obj.add([s1, s3], 'hover');
      var rule2 = obj.get([s3, s1], 'hover');
      expect(rule2).toEqual(rule1);
    });

    it('Returns correct rule with the same selectors, state and width', () => {
      var sel = new obj.Selectors([]);
      var s1 = sel.add({ name: 'test1' });
      var rule1 = obj.add([s1], 'hover', '1');
      var rule2 = obj.get([s1], 'hover', '1');
      expect(rule2).toEqual(rule1);
    });

    it('Renders correctly', () => {
      expect(obj.render()).toExist();
    });

    it('Create a rule with id selector by using setIdRule()', () => {
      const name = 'test';
      obj.setIdRule(name, { color: 'red' });
      expect(obj.getAll().length).toEqual(1);
      const rule = obj.getIdRule(name);
      expect(rule.selectorsToString()).toEqual(`#${name}`);
      expect(rule.styleToString()).toEqual(`color:red;`);
      expect(rule.styleToString({ important: 1 })).toEqual(
        `color:red !important;`
      );
      expect(rule.styleToString({ important: ['color'] })).toEqual(
        `color:red !important;`
      );
    });

    it('Create a rule with id selector and state by using setIdRule()', () => {
      const name = 'test';
      const state = 'hover';
      obj.setIdRule(name, { color: 'red' }, { state });
      expect(obj.getAll().length).toEqual(1);
      const rule = obj.getIdRule(name, { state });
      expect(rule.selectorsToString()).toEqual(`#${name}:${state}`);
    });

    it('Create a rule with class selector by using setClassRule()', () => {
      const name = 'test';
      obj.setClassRule(name, { color: 'red' });
      expect(obj.getAll().length).toEqual(1);
      const rule = obj.getClassRule(name);
      expect(rule.selectorsToString()).toEqual(`.${name}`);
      expect(rule.styleToString()).toEqual(`color:red;`);
    });

    it('Create a rule with class selector and state by using setClassRule()', () => {
      const name = 'test';
      const state = 'hover';
      obj.setClassRule(name, { color: 'red' }, { state });
      expect(obj.getAll().length).toEqual(1);
      const rule = obj.getClassRule(name, { state });
      expect(rule.selectorsToString()).toEqual(`.${name}:${state}`);
    });
  });

  Models.run();
  CssRuleView.run();
  CssRulesView.run();
  e2e.run();
});
