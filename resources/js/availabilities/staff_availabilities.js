document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('working-calendar');
    if (!calendarEl) return;

    const { events, canEdit } = window.availabilityConfig;

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        firstDay: 1,
        timeZone: 'Asia/Ho_Chi_Minh',
        allDaySlot: false,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },

        slotMinTime: '09:00:00',
        slotMaxTime: '22:00:00',
        slotDuration: '01:00:00',
        snapDuration: '01:00:00',

        businessHours: {
            daysOfWeek: [0,1,2,3,4,5,6],
            startTime: '09:00',
            endTime: '20:00'
        },

        selectable: canEdit,
        selectConstraint: 'businessHours',

        selectAllow(info) {
            const d = new Date(info.start);
            d.setHours(0, 0, 0, 0);
            return d >= today;
        },

        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },

        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },

        height: 'auto',
        expandRows: false,

        events: events,
        eventOverlap: false,
        slotEventOverlap: false,

        select(info) {
            if (!canEdit) return;

            const start = info.start;
            const now = new Date();

            if (start < now) {
                alert('Không thể tạo lịch trong quá khứ');
                calendar.unselect();
                return;
            }

            const end = new Date(start);
            end.setHours(end.getHours() + 3);

            const hasDayOff = calendar.getEvents().some(ev =>
                ev.extendedProps?.is_day_off &&
                start < ev.end &&
                end > ev.start
            );

            if (hasDayOff) {
                alert('Ngày này là ngày nghỉ');
                calendar.unselect();
                return;
            }

            const conflict = calendar.getEvents().some(ev =>
                !ev.extendedProps?.is_day_off &&
                start < ev.end &&
                end > ev.start
            );

            if (conflict) {
                alert('Khung giờ này đã có lịch');
                calendar.unselect();
                return;
            }

            calendar.addEvent({
                start,
                end,
                backgroundColor: '#07d100ff',
                borderColor: '#07d100ff'
            });

            calendar.unselect();
        },

        eventClick(info) {
            if (!canEdit) return;
            if (info.event.extendedProps?.is_day_off) return;

            if (confirm('Xoá khung giờ này?')) {
                info.event.remove();
            }
        }
    });

    calendar.render();

    if (!canEdit) return;

    const saveBtn = document.getElementById('save-calendar');
    const form = document.getElementById('availability-form');
    const input = document.getElementById('availability-input');

    if (!saveBtn || !form || !input) return;

    saveBtn.addEventListener('click', function () {
        const payload = {};

        calendar.getEvents().forEach(ev => {
            if (ev.extendedProps?.is_day_off) return;

            const [date, time] = ev.startStr.split('T');
            const hour = time.slice(0, 5);

            if (!payload[date]) payload[date] = [];
            payload[date].push(hour);
        });

        input.value = JSON.stringify(payload);
        form.submit();
    });
});
