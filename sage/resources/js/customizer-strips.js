/* Native WordPress Customizer control; no third-party repeater dependency. */
(function (api, config) {
  'use strict';
  const labels = config.labels;
  api.controlConstructor['sage-product-strips'] = api.Control.extend({
    ready: function () {
      const control = this;
      const root = control.container[0].querySelector('.sage-strips-editor');
      const parse = value => { try { const rows = JSON.parse(value); return Array.isArray(rows) ? rows : []; } catch { return []; } };
      let rows = parse(control.setting.get());
      let writing = false;
      const save = () => { writing = true; control.setting.set(JSON.stringify(rows)); writing = false; };
      const button = (text, action, disabled = false) => {
        const el = document.createElement('button'); el.type = 'button'; el.className = 'button';
        el.textContent = text; el.disabled = disabled; el.addEventListener('click', action); return el;
      };
      const render = (focusId, action = 'heading') => {
        root.replaceChildren();
        rows.forEach((row, index) => {
          const box = document.createElement('fieldset'); box.className = 'sage-strip-editor';
          const legend = document.createElement('legend'); legend.textContent = `${labels.block} ${index + 1}`; box.append(legend);
          function field(key, label, type = 'text', options = null) {
            const wrap = document.createElement('label'); wrap.textContent = label;
            const input = document.createElement(options ? 'select' : 'input');
            input.dataset.row = row.id; input.dataset.field = key;
            if (options) options.forEach(([value, text]) => { const option = document.createElement('option'); option.value = value; option.textContent = text; input.append(option); });
            else input.type = type;
            if (type === 'checkbox') input.checked = Boolean(row[key]); else input.value = row[key] ?? '';
            if (type === 'number') { input.min = key === 'count' ? '1' : '2'; input.max = key === 'count' ? '24' : '6'; input.step = '1'; }
            input.addEventListener('change', () => {
              row[key] = type === 'checkbox' ? input.checked : (type === 'number' || key === 'category') ? Number(input.value) : input.value;
              if (type === 'number') { row[key] = Math.max(Number(input.min), Math.min(Number(input.max), Number.isFinite(row[key]) ? row[key] : Number(input.min))); input.value = row[key]; }
              save();
            });
            wrap.append(input); box.append(wrap);
          }
          field('enabled', labels.enabled, 'checkbox'); field('heading', labels.heading);
          field('source', labels.source, 'text', ['latest', 'category', 'featured', 'sale'].map(key => [key, labels[key]]));
          const categories = [[0, labels.all], ...config.categories.map(term => [term.id, term.name])];
          if (row.category && !config.categories.some(term => Number(term.id) === Number(row.category))) categories.push([row.category, `${labels.missing} #${row.category}`]);
          field('category', labels.categoryLabel, 'text', categories);
          field('count', labels.count, 'number'); field('columns', labels.columns, 'number');
          field('layout', labels.layout, 'text', ['grid', 'scroll'].map(key => [key, labels[key]]));
          const actions = document.createElement('div'); actions.className = 'sage-strip-editor__actions';
          const move = delta => { [rows[index], rows[index + delta]] = [rows[index + delta], rows[index]]; save(); render(row.id, delta < 0 ? 'up' : 'down'); };
          const up = button(labels.up, () => move(-1), index === 0); up.dataset.row = row.id; up.dataset.field = 'up';
          const down = button(labels.down, () => move(1), index === rows.length - 1); down.dataset.row = row.id; down.dataset.field = 'down';
          actions.append(up, down, button(labels.remove, () => { rows.splice(index, 1); save(); render(rows[Math.min(index, rows.length - 1)]?.id); }));
          box.append(actions); root.append(box);
        });
        const add = button(labels.add, () => {
          const row = { id: `strip-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`, enabled: true, heading: '', source: 'latest', category: 0, count: 8, columns: 4, layout: 'scroll' };
          rows.push(row); save(); render(row.id);
        }, rows.length >= 20);
        root.append(add);
        const note = document.createElement('p'); note.textContent = labels.limit; root.append(note);
        if (focusId) {
          const target = [...root.querySelectorAll('[data-row]')].find(el => el.dataset.row === focusId && el.dataset.field === action && !el.disabled)
            || [...root.querySelectorAll('[data-row]')].find(el => el.dataset.row === focusId && el.dataset.field === 'heading');
          target?.focus();
        } else if (action !== 'initial') add.focus();
      };
      control.setting.bind(value => { if (!writing) { rows = parse(value); render(undefined, 'initial'); } });
      render(undefined, 'initial');
    },
  });
})(wp.customize, sageStripControl);
