Vue.component('pos-clock', {
    data: function () {
        return {
            clockNow: Date.now(),
            clockInterval: null,
        };
    },
    computed: {
        nowDate() {
            const d = new Date(this.clockNow);
            const opts = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
            return d.toLocaleDateString(undefined, opts);
        },
        nowTime() {
            const d = new Date(this.clockNow);
            return d.toLocaleTimeString(undefined, { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },
        hoursLeftInDay() {
            const now = new Date(this.clockNow);
            const end = new Date(now);
            end.setHours(23, 59, 59, 999);
            const ms = end - now;
            return Math.max(0, (ms / (1000 * 60 * 60))).toFixed(1);
        },
    },
    mounted() {
        var self = this;
        this.clockInterval = setInterval(function () {
            self.clockNow = Date.now();
        }, 1000);
    },
    beforeDestroy() {
        if (this.clockInterval) {
            clearInterval(this.clockInterval);
        }
    },
    template: `
        <div class="pos-clock pos-datetime-row">
            <div class="pos-datetime-card pos-clock-date">
                <small class="d-block text-muted">Date</small>
                <strong>{{ nowDate }}</strong>
            </div>
            <div class="pos-datetime-card pos-clock-time">
                <small class="d-block text-muted">Time</small>
                <strong>{{ nowTime }}</strong>
            </div>
            <div class="pos-datetime-card pos-clock-target-hours">
                <small class="d-block text-muted">Hours left to fulfill target</small>
                <strong>{{ hoursLeftInDay }} hrs</strong>
            </div>
        </div>
    `,
});

// Inject component styles once
(function () {
    if (document.getElementById('pos-clock-styles')) return;
    var style = document.createElement('style');
    style.id = 'pos-clock-styles';
    style.textContent = [
        '.pos-clock.pos-datetime-row { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }',
        '.pos-clock .pos-datetime-card { border: 1px solid #dee2e6; border-radius: 4px; padding: 6px 10px; min-width: 100px; }',
        '.pos-clock .pos-datetime-card small { font-size: 11px; }',
        '.pos-clock-date { background: #f8f9fa; }',
        '.pos-clock-time { background: #f8f9fa; }',
        '.pos-clock-hours-left { background: #e8f4fd; }',
        '.pos-clock-target-hours { background: #fff3e0; }',
    ].join('\n');
    document.head.appendChild(style);
})();
