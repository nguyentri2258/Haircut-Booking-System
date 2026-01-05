window.showBookingSuccess = function (data) {
    const d = data.booking_details;

    const successModalEl = document.getElementById('bookingSuccessModal');
    if (!successModalEl) {
        console.error('Không tìm thấy bookingSuccessModal');
        return;
    }

    document.getElementById('modal-booking-details').innerHTML = `
        <ul class="list-unstyled">
            <li><strong>Tên:</strong> ${d.name}</li>
            <li><strong>SĐT:</strong> ${d.phone}</li>
            <li><strong>Email:</strong> ${d.email}</li>
            <li><strong>Địa chỉ:</strong> ${d.address}</li>
            <li><strong>Dịch vụ:</strong> ${d.service}</li>
            <li><strong>Tổng:</strong> ${Number(d.price).toLocaleString()}đ</li>
            <li><strong>Stylist:</strong> ${d.user}</li>
            <li><strong>Ngày:</strong> ${d.date}</li>
            ${d.notes ? `<li><strong>Ghi chú:</strong> ${d.notes}</li>` : ''}
        </ul>
    `;

    new bootstrap.Modal(successModalEl).show();
    successModalEl.addEventListener(
        'hidden.bs.modal',
        () => {
            window.location.reload();
        },
        { once: true }
    );
};
