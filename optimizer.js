// ====================================================
//  Production Optimizer — optimizer.js
//  LP solver + SVG graph + PHP/MySQL API
// ====================================================

var SVG_NS = 'http://www.w3.org/2000/svg';
var API_BASE = 'api';

// ── State ──────────────────────────────────────────
var state = {
    currentProblemId: null,
    problemName: 'Untitled Problem',
    budget: 315,
    budgetPeriod: 'week',
    workHours: 40,
    workHoursUnit: 'hrs',
    productA: { name: 'Table (X)', sale: 90, cost: 15, time: 2, timeUnit: 'hrs' },
    productB: { name: 'Chair (Y)', sale: 180, cost: 45, time: 5, timeUnit: 'hrs' },
    constraints: [],
    savedProblems: [],
    lastResult: null
};

// ── Utilities ──────────────────────────────────────
function esc(str) {
    var d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}

function toast(msg, type) {
    type = type || 'success';
    var c = document.getElementById('toast-container');
    if (!c) return;
    var t = document.createElement('div');
    t.className = 'toast toast-' + type;
    t.textContent = msg;
    c.appendChild(t);
    requestAnimationFrame(function(){ t.classList.add('show'); });
    setTimeout(function(){ t.classList.remove('show'); setTimeout(function(){ t.remove(); }, 300); }, 3200);
}

// ── Derive constraints from inputs ──────────────────
function deriveConstraints() {
    state.budget = parseFloat(document.getElementById('wiz-budget').value) || 0;
    state.workHours = parseFloat(document.getElementById('wiz-hours').value) || 0;
    state.workHoursUnit = document.getElementById('wiz-hours-unit').value;
    state.budgetPeriod = document.getElementById('wiz-period').value;

    var workHrs = state.workHours;
    if (state.workHoursUnit === 'min') workHrs /= 60;
    if (state.workHoursUnit === 'sec') workHrs /= 3600;

    state.productA.sale = parseFloat(document.getElementById('prod-a-sale').value) || 0;
    state.productA.cost = parseFloat(document.getElementById('prod-a-cost').value) || 0;
    state.productB.sale = parseFloat(document.getElementById('prod-b-sale').value) || 0;
    state.productB.cost = parseFloat(document.getElementById('prod-b-cost').value) || 0;
    state.productA.time = parseFloat(document.getElementById('prod-a-time').value) || 0;
    state.productA.timeUnit = document.getElementById('prod-a-time-unit').value;
    state.productB.time = parseFloat(document.getElementById('prod-b-time').value) || 0;
    state.productB.timeUnit = document.getElementById('prod-b-time-unit').value;

    var timeAHrs = state.productA.time;
    if (state.productA.timeUnit === 'min') timeAHrs /= 60;
    if (state.productA.timeUnit === 'sec') timeAHrs /= 3600;

    var timeBHrs = state.productB.time;
    if (state.productB.timeUnit === 'min') timeBHrs /= 60;
    if (state.productB.timeUnit === 'sec') timeBHrs /= 3600;

    state.constraints = [
        {
            name: 'Budget (' + state.budgetPeriod + ')',
            coefA: state.productA.cost,
            coefB: state.productB.cost,
            max: state.budget,
            unit: 'RM'
        },
        {
            name: 'Work Hours (' + state.budgetPeriod + ')',
            coefA: timeAHrs,
            coefB: timeBHrs,
            max: workHrs,
            unit: 'hrs'
        }
    ];
}

// ── Boot ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    setupEvents();
    loadProblems().then(function() {
        var params = new URLSearchParams(window.location.search);
        var loadId = params.get('load');
        if (loadId) {
            var problem = state.savedProblems.find(function(p){ return parseInt(p.id) === parseInt(loadId); });
            if (problem) loadProblem(problem);
        }
    });
});

// ====================================================
//  API
// ====================================================
function loadProblems() {
    return fetch(API_BASE + '/get_problems.php')
        .then(function(r){ if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(function(data){ state.savedProblems = data; })
        .catch(function(e){ console.warn('loadProblems:', e); state.savedProblems = []; });
}

function saveProblem() {
    deriveConstraints();
    var name = document.getElementById('wiz-name').value.trim();
    if (!name) { toast('Enter a problem name', 'error'); return; }

    var payload = {
        id: state.currentProblemId,
        name: name,
        prod_a_name: state.productA.name,
        prod_a_sale: state.productA.sale,
        prod_a_cost: state.productA.cost,
        prod_a_time: state.productA.time,
        prod_a_time_unit: state.productA.timeUnit,
        prod_b_name: state.productB.name,
        prod_b_sale: state.productB.sale,
        prod_b_cost: state.productB.cost,
        prod_b_time: state.productB.time,
        prod_b_time_unit: state.productB.timeUnit,
        budget: state.budget,
        budget_period: state.budgetPeriod,
        work_hours: state.workHours,
        work_hours_unit: state.workHoursUnit,
        constraints: state.constraints.map(function(c){
            return { name: c.name, coef_a: c.coefA, coef_b: c.coefB, max_val: c.max, unit: c.unit };
        })
    };

    fetch(API_BASE + '/save_problem.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(function(r){
        return r.json().then(function(data){
            if (!r.ok) throw new Error(data.error || 'HTTP ' + r.status);
            return data;
        });
    })
    .then(function(r){
        if (r.success) {
            var wasNew = !state.currentProblemId;
            state.currentProblemId = r.id;
            toast(wasNew ? 'Problem saved' : 'Problem updated');
            window.history.replaceState({}, '', 'optimizer.php?load=' + r.id);
            loadProblems();
        } else {
            throw new Error(r.error || 'Unknown error');
        }
    })
    .catch(function(e){ toast('Save failed: ' + e.message, 'error'); });
}

// ── Load saved problem ──────────────────────────────
function loadProblem(problem) {
    state.currentProblemId = parseInt(problem.id);
    state.problemName = problem.name;
    state.budget = parseFloat(problem.budget) || 0;
    state.budgetPeriod = problem.budget_period || 'week';
    state.workHours = parseFloat(problem.work_hours) || 0;
    state.workHoursUnit = problem.work_hours_unit || 'hrs';

    state.productA = {
        name: problem.prod_a_name || 'Product A',
        sale: parseFloat(problem.prod_a_sale) || 0,
        cost: parseFloat(problem.prod_a_cost) || 0,
        time: parseFloat(problem.prod_a_time) || 0,
        timeUnit: problem.prod_a_time_unit || 'hrs'
    };
    state.productB = {
        name: problem.prod_b_name || 'Product B',
        sale: parseFloat(problem.prod_b_sale) || 0,
        cost: parseFloat(problem.prod_b_cost) || 0,
        time: parseFloat(problem.prod_b_time) || 0,
        timeUnit: problem.prod_b_time_unit || 'hrs'
    };

    document.getElementById('wiz-name').value = state.problemName;
    document.getElementById('wiz-budget').value = state.budget;
    document.getElementById('wiz-period').value = state.budgetPeriod;
    document.getElementById('wiz-hours').value = state.workHours;
    document.getElementById('wiz-hours-unit').value = state.workHoursUnit;

    document.getElementById('prod-a-name').value = state.productA.name;
    document.getElementById('prod-a-sale').value = state.productA.sale;
    document.getElementById('prod-a-cost').value = state.productA.cost;
    document.getElementById('prod-a-time').value = state.productA.time;
    document.getElementById('prod-a-time-unit').value = state.productA.timeUnit;

    document.getElementById('prod-b-name').value = state.productB.name;
    document.getElementById('prod-b-sale').value = state.productB.sale;
    document.getElementById('prod-b-cost').value = state.productB.cost;
    document.getElementById('prod-b-time').value = state.productB.time;
    document.getElementById('prod-b-time-unit').value = state.productB.timeUnit;

    deriveConstraints();
    solveAndRender();
}

function newProblem() {
    state.currentProblemId = null;
    state.problemName = 'Untitled Problem';
    state.budget = 0;
    state.budgetPeriod = 'week';
    state.workHours = 0;
    state.workHoursUnit = 'hrs';
    state.productA = { name: 'Product A', sale: 0, cost: 0, time: 0, timeUnit: 'hrs' };
    state.productB = { name: 'Product B', sale: 0, cost: 0, time: 0, timeUnit: 'hrs' };
    state.lastResult = null;

    document.getElementById('wiz-name').value = 'Untitled Problem';
    document.getElementById('wiz-budget').value = 0;
    document.getElementById('wiz-period').value = 'week';
    document.getElementById('wiz-hours').value = 0;
    document.getElementById('wiz-hours-unit').value = 'hrs';

    document.getElementById('prod-a-name').value = 'Product A';
    document.getElementById('prod-a-sale').value = 0;
    document.getElementById('prod-a-cost').value = 0;
    document.getElementById('prod-a-time').value = 0;
    document.getElementById('prod-a-time-unit').value = 'hrs';

    document.getElementById('prod-b-name').value = 'Product B';
    document.getElementById('prod-b-sale').value = 0;
    document.getElementById('prod-b-cost').value = 0;
    document.getElementById('prod-b-time').value = 0;
    document.getElementById('prod-b-time-unit').value = 'hrs';

    deriveConstraints();
    document.getElementById('results-area').style.display = 'none';
    window.history.replaceState({}, '', 'optimizer.php');
}

// ====================================================
//  Event Listeners
// ====================================================
function setupEvents() {
    document.getElementById('wiz-name').addEventListener('input', function(e){ state.problemName = e.target.value; });
    document.getElementById('prod-a-name').addEventListener('input', function(e){ state.productA.name = e.target.value || 'Product A'; });
    document.getElementById('prod-b-name').addEventListener('input', function(e){ state.productB.name = e.target.value || 'Product B'; });
    document.getElementById('btn-calculate').addEventListener('click', function(){
        deriveConstraints();
        solveAndRender();
        document.getElementById('results-area').style.display = 'block';
        document.getElementById('results-area').scrollIntoView({ behavior: 'smooth' });
    });
    document.getElementById('btn-new').addEventListener('click', newProblem);
    document.getElementById('btn-save-problem').addEventListener('click', saveProblem);
}

// ====================================================
//  LP Solver — Vertex Enumeration
// ====================================================
function solveLP() {
    var saleA = state.productA.sale;
    var saleB = state.productB.sale;
    var constraints = state.constraints;

    var candidates = [{ x: 0, y: 0 }];

    constraints.forEach(function(c){
        if (c.coefA > 0) candidates.push({ x: c.max / c.coefA, y: 0 });
        if (c.coefB > 0) candidates.push({ x: 0, y: c.max / c.coefB });
    });

    for (var i = 0; i < constraints.length; i++) {
        for (var j = i + 1; j < constraints.length; j++) {
            var c1 = constraints[i], c2 = constraints[j];
            var det = c1.coefA * c2.coefB - c1.coefB * c2.coefA;
            if (Math.abs(det) > 1e-8) {
                var x = (c1.max * c2.coefB - c2.max * c1.coefB) / det;
                var y = (c1.coefA * c2.max - c2.coefA * c1.max) / det;
                candidates.push({ x: x, y: y });
            }
        }
    }

    var unique = [];
    candidates.forEach(function(p){
        if (p.x < 0 && p.x > -1e-6) p.x = 0;
        if (p.y < 0 && p.y > -1e-6) p.y = 0;
        var dup = unique.some(function(u){ return Math.abs(u.x - p.x) < 1e-5 && Math.abs(u.y - p.y) < 1e-5; });
        if (!dup) unique.push(p);
    });

    var vertices = unique.map(function(p){
        var feasible = true;
        var violated = [];
        if (p.x < -1e-7) { feasible = false; violated.push('X \u2265 0'); }
        if (p.y < -1e-7) { feasible = false; violated.push('Y \u2265 0'); }
        constraints.forEach(function(c){
            if (c.coefA * p.x + c.coefB * p.y > c.max + 1e-5) {
                feasible = false;
                violated.push(c.name);
            }
        });
        var value = feasible ? saleA * p.x + saleB * p.y : null;
        return { x: Math.max(0, p.x), y: Math.max(0, p.y), feasible: feasible, value: value, violated: violated };
    });

    var optimal = null, maxValue = -Infinity;
    vertices.forEach(function(v){
        if (v.feasible && v.value !== null && v.value > maxValue) { maxValue = v.value; optimal = v; }
    });

    return { vertices: vertices, optimal: optimal, saleA: saleA, saleB: saleB };
}

// ====================================================
//  Solve + Render
// ====================================================
function solveAndRender() {
    var result = solveLP();
    state.lastResult = result;

    // Formulation
    document.getElementById('math-objective').textContent =
        'Max S = ' + Math.round(result.saleA) + 'X + ' + Math.round(result.saleB) + 'Y';

    var mc = document.getElementById('math-constraints');
    mc.innerHTML = '';
    state.constraints.forEach(function(c){
        var d = document.createElement('div');
        d.className = 'equation';
        d.textContent = c.coefA + 'X + ' + c.coefB + 'Y \u2264 ' + c.max + '  (' + c.name + ')';
        mc.appendChild(d);
    });

    // Corner point table
    var tbody = document.getElementById('corner-table-body');
    tbody.innerHTML = '';
    var opt = result.optimal;
    result.vertices.forEach(function(v){
        var isOpt = opt && Math.abs(v.x - opt.x) < 1e-5 && Math.abs(v.y - opt.y) < 1e-5;
        var tr = document.createElement('tr');
        if (isOpt) tr.className = 'row-optimal';
        else if (!v.feasible) tr.className = 'row-infeasible';

        var statusHtml = v.feasible
            ? '<span style="color:#3ecfb2;font-weight:500;">Feasible</span>'
            : '<span style="color:#e05252;">Infeasible</span>';
        var valText = v.feasible ? '$' + v.value.toFixed(2) : '\u2014';
        var analysis = '';
        if (isOpt) analysis = '\u2605 Optimal \u2014 maximizes total sales';
        else if (!v.feasible) analysis = 'Violates: ' + v.violated.join(', ');
        else analysis = 'Feasible, lower sales value';

        tr.innerHTML =
            '<td>(' + v.x.toFixed(2) + ', ' + v.y.toFixed(2) + ')</td>' +
            '<td>' + statusHtml + '</td>' +
            '<td>' + valText + '</td>' +
            '<td>' + analysis + '</td>';
        tbody.appendChild(tr);
    });

    // Optimal result
    var optDiv = document.getElementById('optimal-result');
    if (opt && opt.value > 0) {
        var prodAName = state.productA.name.replace(/\(.*\)/, '').trim();
        var prodBName = state.productB.name.replace(/\(.*\)/, '').trim();
        optDiv.innerHTML =
            '<p class="opt-text">Make <strong class="opt-num">' + opt.x.toFixed(1) + '</strong> ' + esc(prodAName) +
            ' and <strong class="opt-num">' + opt.y.toFixed(1) + '</strong> ' + esc(prodBName) + '</p>' +
            '<p class="opt-text">Maximum Sales Revenue: <strong class="opt-num-gold">$' + opt.value.toFixed(2) +
            '</strong> per ' + state.budgetPeriod + '</p>';
    } else {
        optDiv.innerHTML = '<p class="opt-text" style="color:var(--red);">No feasible solution found. Adjust budget or hours.</p>';
    }

    // Graph
    drawGraph(result);
}

// ====================================================
//  SVG Graph
// ====================================================
function drawGraph(result) {
    var svg = document.getElementById('svg-graph');
    svg.innerHTML = '';

    var W = 520, H = 360;
    var pad = { left: 52, right: 20, top: 20, bottom: 44 };
    var plotW = W - pad.left - pad.right;
    var plotH = H - pad.top - pad.bottom;

    var maxX = 10, maxY = 10;
    state.constraints.forEach(function(c){
        if (c.coefA > 0) maxX = Math.max(maxX, c.max / c.coefA);
        if (c.coefB > 0) maxY = Math.max(maxY, c.max / c.coefB);
    });
    maxX = Math.ceil(maxX * 1.25);
    maxY = Math.ceil(maxY * 1.25);

    var sx = function(x){ return pad.left + (x / maxX) * plotW; };
    var sy = function(y){ return pad.top + plotH - (y / maxY) * plotH; };

    function el(tag, attrs){
        var e = document.createElementNS(SVG_NS, tag);
        for (var k in attrs) e.setAttribute(k, attrs[k]);
        return e;
    }

    svg.appendChild(el('rect', { x: pad.left, y: pad.top, width: plotW, height: plotH, class: 'svg-bg' }));

    var step = function(v){ return v <= 10 ? 1 : v <= 30 ? 5 : v <= 100 ? 10 : v <= 300 ? 25 : 50; };
    var stX = step(maxX), stY = step(maxY);

    for (var x = stX; x <= maxX; x += stX) {
        svg.appendChild(el('line', { x1: sx(x), y1: pad.top, x2: sx(x), y2: pad.top + plotH, class: 'grid-line' }));
        var tx = el('text', { x: sx(x), y: pad.top + plotH + 14, 'text-anchor': 'middle', class: 'axis-label' });
        tx.textContent = x; svg.appendChild(tx);
    }
    var t0x = el('text', { x: sx(0), y: pad.top + plotH + 14, 'text-anchor': 'middle', class: 'axis-label' });
    t0x.textContent = '0'; svg.appendChild(t0x);

    for (var y = stY; y <= maxY; y += stY) {
        svg.appendChild(el('line', { x1: pad.left, y1: sy(y), x2: pad.left + plotW, y2: sy(y), class: 'grid-line' }));
        var ty = el('text', { x: pad.left - 8, y: sy(y) + 4, 'text-anchor': 'end', class: 'axis-label' });
        ty.textContent = y; svg.appendChild(ty);
    }
    var t0y = el('text', { x: pad.left - 8, y: sy(0) + 4, 'text-anchor': 'end', class: 'axis-label' });
    t0y.textContent = '0'; svg.appendChild(t0y);

    var fverts = result.vertices.filter(function(v){ return v.feasible; });
    if (fverts.length >= 3) {
        var cx = 0, cy = 0;
        fverts.forEach(function(v){ cx += v.x; cy += v.y; });
        cx /= fverts.length; cy /= fverts.length;
        fverts.sort(function(a, b){ return Math.atan2(a.y - cy, a.x - cx) - Math.atan2(b.y - cy, b.x - cx); });
        var pts = fverts.map(function(v){ return sx(v.x) + ',' + sy(v.y); }).join(' ');
        svg.appendChild(el('polygon', { points: pts, class: 'feasible-poly' }));
    } else if (fverts.length === 2) {
        svg.appendChild(el('line', { x1: sx(fverts[0].x), y1: sy(fverts[0].y), x2: sx(fverts[1].x), y2: sy(fverts[1].y), stroke: 'rgba(212,168,83,0.4)', 'stroke-width': 2 }));
    }

    var cColors = ['#d4a853', '#3ecfb2', '#3b82f6', '#e05252', '#a855f7', '#e07c3e'];
    state.constraints.forEach(function(c, i){
        var col = cColors[i % cColors.length];
        var p1, p2;
        if (c.coefA === 0 && c.coefB > 0) { var yv = c.max / c.coefB; p1 = { x: 0, y: yv }; p2 = { x: maxX, y: yv }; }
        else if (c.coefB === 0 && c.coefA > 0) { var xv = c.max / c.coefA; p1 = { x: xv, y: 0 }; p2 = { x: xv, y: maxY }; }
        else if (c.coefA > 0 && c.coefB > 0) { p1 = { x: 0, y: c.max / c.coefB }; p2 = { x: c.max / c.coefA, y: 0 }; }
        else return;
        svg.appendChild(el('line', { x1: sx(p1.x), y1: sy(p1.y), x2: sx(p2.x), y2: sy(p2.y), stroke: col, class: 'constraint-line' }));
    });

    var opt = result.optimal;
    if (opt && opt.value > 0) {
        var sa = result.saleA, sb = result.saleB;
        var ip1, ip2;
        if (sa === 0 && sb > 0) { var yv2 = opt.value / sb; ip1 = { x: 0, y: yv2 }; ip2 = { x: maxX, y: yv2 }; }
        else if (sb === 0 && sa > 0) { var xv2 = opt.value / sa; ip1 = { x: xv2, y: 0 }; ip2 = { x: xv2, y: maxY }; }
        else if (sa > 0 && sb > 0) { ip1 = { x: 0, y: opt.value / sb }; ip2 = { x: opt.value / sa, y: 0 }; }
        if (ip1 && ip2) {
            svg.appendChild(el('line', { x1: sx(ip1.x), y1: sy(ip1.y), x2: sx(ip2.x), y2: sy(ip2.y), class: 'isoprofit-line' }));
        }
    }

    var tooltip = document.getElementById('graph-tooltip');
    result.vertices.forEach(function(v){
        var isOpt = opt && Math.abs(v.x - opt.x) < 1e-5 && Math.abs(v.y - opt.y) < 1e-5;
        var r = isOpt ? 6 : 4;
        var circle = el('circle', {
            cx: sx(v.x), cy: sy(v.y), r: r,
            class: 'vertex-point ' + (isOpt ? 'vertex-optimal' : v.feasible ? 'vertex-feasible' : 'vertex-infeasible'),
            stroke: isOpt ? 'var(--bg)' : v.feasible ? 'rgba(212,168,83,0.4)' : 'rgba(255,255,255,0.15)'
        });
        var showTip = function(){
            var html = '(' + v.x.toFixed(2) + ', ' + v.y.toFixed(2) + ')<br>';
            html += v.feasible ? 'Sales: $' + v.value.toFixed(2) : 'Violates: ' + v.violated.join(', ');
            if (isOpt) html += '<br>&starf; OPTIMAL';
            tooltip.innerHTML = html;
            tooltip.style.left = (parseFloat(circle.getAttribute('cx')) + 12) + 'px';
            tooltip.style.top = (parseFloat(circle.getAttribute('cy')) - 12) + 'px';
            tooltip.style.display = 'block';
        };
        circle.addEventListener('mouseenter', showTip);
        circle.addEventListener('mousemove', showTip);
        circle.addEventListener('mouseleave', function(){ tooltip.style.display = 'none'; });
        svg.appendChild(circle);
    });

    svg.appendChild(el('line', { x1: pad.left, y1: pad.top, x2: pad.left, y2: pad.top + plotH, class: 'axis-line' }));
    svg.appendChild(el('line', { x1: pad.left, y1: pad.top + plotH, x2: pad.left + plotW, y2: pad.top + plotH, class: 'axis-line' }));

    var xTitle = el('text', { x: pad.left + plotW / 2, y: H - 8, 'text-anchor': 'middle', class: 'axis-title' });
    xTitle.textContent = state.productA.name.replace(/\(.*\)/, '').trim() + ' (X)';
    svg.appendChild(xTitle);

    var yGroup = el('g', { transform: 'translate(14, ' + (pad.top + plotH / 2) + ') rotate(-90)' });
    var yTitle = el('text', { 'text-anchor': 'middle', class: 'axis-title' });
    yTitle.textContent = state.productB.name.replace(/\(.*\)/, '').trim() + ' (Y)';
    yGroup.appendChild(yTitle);
    svg.appendChild(yGroup);
}
