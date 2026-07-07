/**
 * personal.js – Kegiatan Anggota (Google Calendar view)
 * Uses FullCalendar to render events and modals for CRUD.
 */

var calendarInstance;
var save_method;
let myData = {};
const urls = "/ketersediaan-pribadi";
let personalDateIndex = 0;
let personalSlotIndex = 0;

const defaultStartTime = "08:00";
const defaultEndTime = "17:00";
const fullDayStartTime = "00:00";
const fullDayEndTime = "23:59";

// Current detail event (for edit / delete from detail modal)
let currentDetailEventId = null;

jQuery(function () {
    myData._token = $('meta[name="csrf-token"]').attr("content");
});

$(document).ready(function () {
    initCalendar();
    resetPersonalForm("add");
});

/* ===================================================================
   FullCalendar Initialization
   =================================================================== */
function initCalendar() {
    var calendarEl = document.getElementById("calendarKegiatan");
    if (!calendarEl) return;

    calendarInstance = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "id",
        firstDay: 1, // Monday
        height: "auto",
        dayMaxEvents: 3,
        weekends: true,
        selectable: false,
        headerToolbar: false, // We use our custom toolbar

        // Load events via jQuery AJAX (sends X-Requested-With header for Laravel)
        events: function (info, successCallback, failureCallback) {
            $.ajax({
                url: urls,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    successCallback(data);
                },
                error: function () {
                    toastr.error("Gagal memuat data kegiatan.");
                    failureCallback();
                },
            });
        },

        // Click on a date → open Add modal with date prefilled
        dateClick: function (info) {
            save_method = "add";
            resetPersonalForm("add");
            // Pre-fill the date in the first date card
            var firstDateInput = $(
                "#personalRows .personal-date-card:first .personal-date-input"
            );
            if (firstDateInput.length) {
                firstDateInput.val(info.dateStr);
            }
            $(".modal-title").text("Tambah Kegiatan Baru");
            $("#modalPersonal").modal("show");
        },

        // Click on an event → open Detail modal
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            openDetailModal(info.event);
        },

        // Custom event rendering with tooltip
        eventDidMount: function (info) {
            var props = info.event.extendedProps || {};
            var tipText = (props.nama || "") + "\n";
            tipText += (props.tanggal || "") + "\n";
            tipText += (props.waktu_mulai || "") + " - " + (props.waktu_selesai || "");
            info.el.setAttribute("title", tipText);
        },

        // Update month display when dates change
        datesSet: function (info) {
            updateMonthDisplay(info.view);
        },
    });

    calendarInstance.render();
    setupToolbarHandlers();
}

/* ===================================================================
   Custom Toolbar Handlers
   =================================================================== */
function setupToolbarHandlers() {
    // Previous
    $(document).on("click", "#calPrev", function () {
        calendarInstance.prev();
    });

    // Next
    $(document).on("click", "#calNext", function () {
        calendarInstance.next();
    });

    // Today
    $(document).on("click", "#calToday", function () {
        calendarInstance.today();
    });

    // View toggle buttons
    $(document).on("click", ".vtoggle-btn", function () {
        var view = $(this).data("view");
        calendarInstance.changeView(view);
        $(".vtoggle-btn").removeClass("active");
        $(this).addClass("active");
    });
}

function updateMonthDisplay(view) {
    var title = view.title; // FullCalendar auto-formats with locale
    $("#calMonthDisplay").text(title);
}

/* ===================================================================
   Detail Modal (view event info)
   =================================================================== */
function openDetailModal(event) {
    var props = event.extendedProps || {};

    currentDetailEventId = props.ketersediaan_id || event.id;

    $("#detailDot").css("background", event.backgroundColor || "#4e73df");
    $("#detailEventTitle").text(props.nama || "Kegiatan");
    $("#detailNama").text(props.nama || "-");
    $("#detailTanggal").text(props.tanggal || "-");

    var waktu =
        props.is_full_day
            ? "Seharian (00:00 - 23:59)"
            : (props.waktu_mulai || "-") + " - " + (props.waktu_selesai || "-");
    $("#detailWaktu").text(waktu);

    $("#modalEventDetail").modal("show");
}

// Edit from detail modal
$(document).on("click", "#btnEditEvent", function () {
    if (!currentDetailEventId) return;
    $("#modalEventDetail").modal("hide");
    showSelectedData(currentDetailEventId);
});

// Delete from detail modal
$(document).on("click", "#btnDeleteEvent", function () {
    if (!currentDetailEventId) return;
    $("#modalEventDetail").modal("hide");
    deleteKegiatan(currentDetailEventId);
});

/* ===================================================================
   Add Button (top toolbar)
   =================================================================== */
$(document).on("click", ".addPersonal", function () {
    save_method = "add";
    resetPersonalForm("add");
    $("#modalPersonal").modal("show");
    $(".modal-title").text("Tambah Data Jadwal Pribadi");
});

/* ===================================================================
   Form Row Handlers (tanggal / waktu)
   =================================================================== */
$(document).on("click", ".addTanggalRow", function () {
    $("#personalRows").append(createTanggalCard());
});

$(document).on("click", ".addWaktuRow", function () {
    var card = $(this).closest(".personal-date-card");
    var dateIndex = card.data("date-index");
    card.find(".personal-slots").append(createSlotRow(dateIndex));
});

$(document).on("click", ".removeTanggalRow", function () {
    if ($("#personalRows .personal-date-card").length === 1) {
        toastr.warning("Minimal satu tanggal harus diisi.");
        return;
    }
    $(this).closest(".personal-date-card").remove();
});

$(document).on("click", ".removeWaktuRow", function () {
    var card = $(this).closest(".personal-date-card");
    if (card.find(".personal-slot-row").length === 1) {
        toastr.warning("Minimal satu rentang jam harus diisi.");
        return;
    }
    $(this).closest(".personal-slot-row").remove();
});

$(document).on("change", ".personal-full-day", function () {
    syncFullDayState($(this).closest(".personal-slot-row"), this.checked);
});

/* ===================================================================
   Modal Reset on Close
   =================================================================== */
$("#modalPersonal").on("hidden.bs.modal", function () {
    resetPersonalForm("add");
    $("#id").val("");
    $(".modal-title").text("");
    save_method = "";

    var form = $("#formPersonal");
    if (form.data("validator")) {
        form.validate().resetForm();
    }
    form.find(".form-control").removeClass("is-invalid");
    form.find(".form-control").removeClass("is-valid");
});

/* ===================================================================
   Form Helpers
   =================================================================== */
function resetPersonalForm(mode) {
    $("#formPersonal")[0].reset();
    $("#personalRows").empty();
    personalDateIndex = 0;
    personalSlotIndex = 0;

    if (mode === "edit") {
        setEditMode(true);
        return;
    }

    setEditMode(false);
    $("#personalRows").append(createTanggalCard());
}

function setEditMode(isEdit) {
    $(".addTanggalRow").toggle(!isEdit);
    $(".removeTanggalRow").toggle(!isEdit);
    $(".addWaktuRow").toggle(!isEdit);
    $(".removeWaktuRow").toggle(!isEdit);
    $(".personal-full-day").prop("disabled", isEdit);
}

function createTanggalCard(data) {
    data = data || {};
    var dateIndex =
        typeof data.dateIndex === "number"
            ? data.dateIndex
            : personalDateIndex++;
    var slots =
        Array.isArray(data.slots) && data.slots.length ? data.slots : [{}];
    var title = data.title || "Tanggal";

    var card = $(
        '<div class="card border mb-3 personal-date-card" data-date-index="' +
            dateIndex +
            '">' +
            '<div class="card-body">' +
            '<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap">' +
            '<div class="flex-grow-1 pr-3" style="min-width: 220px;">' +
            '<label class="form-label">' +
            title +
            ' <span class="text-danger">*</span></label>' +
            '<input type="date" class="form-control personal-date-input" name="items[' +
            dateIndex +
            '][tanggal]" value="' +
            escapeHtml(data.tanggal || "") +
            '" required>' +
            "</div>" +
            '<div class="mt-2 mt-md-0">' +
            '<button type="button" class="btn btn-outline-danger btn-sm removeTanggalRow">Hapus Tanggal</button>' +
            "</div>" +
            "</div>" +
            '<div class="personal-slots"></div>' +
            '<div class="mt-3">' +
            '<button type="button" class="btn btn-outline-primary btn-sm addWaktuRow">Tambah Jam</button>' +
            "</div>" +
            "</div>" +
            "</div>"
    );

    var slotsContainer = card.find(".personal-slots");
    slots.forEach(function (slot) {
        slotsContainer.append(createSlotRow(dateIndex, slot));
    });

    return card;
}

function createSlotRow(dateIndex, slot) {
    slot = slot || {};
    var slotIndex =
        typeof slot.slotIndex === "number"
            ? slot.slotIndex
            : personalSlotIndex++;
    var isFullDay =
        slot.seharian === true || slot.seharian === "1" || slot.seharian === 1;
    var startTime =
        normalizeTime(slot.waktu_mulai) ||
        (isFullDay ? fullDayStartTime : defaultStartTime);
    var endTime =
        normalizeTime(slot.waktu_selesai) ||
        (isFullDay ? fullDayEndTime : defaultEndTime);

    var row = $(
        '<div class="border rounded p-3 mb-2 personal-slot-row" data-slot-index="' +
            slotIndex +
            '">' +
            '<div class="row">' +
            '<div class="col-md-5 mb-3 mb-md-0">' +
            '<label class="form-label">Jam Mulai <span class="text-danger">*</span></label>' +
            '<input type="time" class="form-control personal-start-time" name="items[' +
            dateIndex +
            "][slots][" +
            slotIndex +
            '][waktu_mulai]" value="' +
            startTime +
            '" required>' +
            "</div>" +
            '<div class="col-md-5 mb-3 mb-md-0">' +
            '<label class="form-label">Jam Selesai <span class="text-danger">*</span></label>' +
            '<input type="time" class="form-control personal-end-time" name="items[' +
            dateIndex +
            "][slots][" +
            slotIndex +
            '][waktu_selesai]" value="' +
            endTime +
            '" required>' +
            "</div>" +
            '<div class="col-md-2 d-flex align-items-end">' +
            '<div class="custom-control custom-checkbox mb-2">' +
            '<input type="checkbox" class="custom-control-input personal-full-day" id="full-day-' +
            dateIndex +
            "-" +
            slotIndex +
            '" name="items[' +
            dateIndex +
            "][slots][" +
            slotIndex +
            '][seharian]" value="1" ' +
            (isFullDay ? "checked" : "") +
            ">" +
            '<label class="custom-control-label" for="full-day-' +
            dateIndex +
            "-" +
            slotIndex +
            '">Seharian</label>' +
            "</div>" +
            "</div>" +
            "</div>" +
            '<div class="text-right mt-2">' +
            '<button type="button" class="btn btn-link text-danger p-0 removeWaktuRow">Hapus Jam</button>' +
            "</div>" +
            "</div>"
    );

    if (isFullDay) {
        syncFullDayState(row, true);
    }

    return row;
}

function syncFullDayState(row, checked) {
    var startInput = row.find(".personal-start-time");
    var endInput = row.find(".personal-end-time");

    if (checked) {
        startInput.val(fullDayStartTime);
        endInput.val(fullDayEndTime);
        row.addClass("bg-light");
    } else {
        row.removeClass("bg-light");
        if (
            startInput.val() === fullDayStartTime &&
            endInput.val() === fullDayEndTime
        ) {
            startInput.val(defaultStartTime);
            endInput.val(defaultEndTime);
        }
    }
}

function normalizeTime(value) {
    if (!value) return "";
    return String(value).substring(0, 5);
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/* ===================================================================
   CRUD Operations
   =================================================================== */
function save() {
    var url = urls;
    var method = "POST";
    var formData = new FormData($("#formPersonal")[0]);

    if (save_method === "edit") {
        url = urls + "/" + $("#id").val();
        formData.append("_method", "PUT");
    }

    $.ajax({
        type: method,
        url: url,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $("#modalPersonal").modal("hide");
            if (response.status) {
                // Refresh calendar events
                calendarInstance.refetchEvents();
                toastr.success(response.message);
            }
        },
        error: function (xhr) {
            var message =
                xhr.responseJSON?.message ||
                "Terjadi kesalahan saat menyimpan data.";
            if (xhr.responseJSON?.errors) {
                var firstError = Object.values(xhr.responseJSON.errors)[0];
                if (firstError && firstError.length) {
                    message = firstError[0];
                }
            }

            Swal.fire({
                title: "Error!",
                text: message,
                icon: "error",
            });
        },
    });
}

function showSelectedData(id) {
    $.ajax({
        type: "GET",
        url: urls + "/" + id,
        beforeSend: function () {
            Swal.fire({
                title: "Loading...",
                text: "Mengambil data Pribadi...",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });
        },
        success: function (response) {
            Swal.close();
            const data = response.data || {};

            $("#id").val(data.id || "");
            save_method = "edit";
            resetPersonalForm("edit");
            $("#personalRows").append(
                createTanggalCard({
                    dateIndex: 0,
                    tanggal: data.tanggal || "",
                    slots: [
                        {
                            slotIndex: 0,
                            waktu_mulai: normalizeTime(data.waktu_mulai),
                            waktu_selesai: normalizeTime(data.waktu_selesai),
                            seharian:
                                normalizeTime(data.waktu_mulai) ===
                                    fullDayStartTime &&
                                normalizeTime(data.waktu_selesai) ===
                                    fullDayEndTime,
                        },
                    ],
                })
            );

            setEditMode(true);
            $("#modalPersonal").modal("show");
            $(".modal-title").text("Edit Data Jadwal Pribadi");
        },
        error: function (xhr) {
            Swal.close();
            let message = "Terjadi kesalahan saat mengambil data.";
            if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
            Swal.fire({ title: "Error!", text: message, icon: "error" });
        },
    });
}

function deleteKegiatan(id) {
    Swal.fire({
        title: "Konfirmasi Hapus",
        text: "Apakah Anda yakin ingin menghapus kegiatan ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${urls}/${id}/delete`,
                method: "POST",
                data: {
                    _method: "DELETE",
                    _token: myData._token,
                },
                success: function (res) {
                    if (res.status) {
                        calendarInstance.refetchEvents();
                        toastr.success(res.message);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        title: "Error!",
                        text:
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan saat menghapus data.",
                        icon: "error",
                    });
                },
            });
        }
    });
}
