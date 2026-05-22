var personalTable;
var save_method;
let myData = {};
const urls = "ketersediaan-pribadi";
let personalDateIndex = 0;
let personalSlotIndex = 0;

const defaultStartTime = "08:00";
const defaultEndTime = "17:00";
const fullDayStartTime = "08:00";
const fullDayEndTime = "16:00";

jQuery(function () {
    myData._token = $('meta[name="csrf-token"]').attr("content");
});

$(document).ready(function () {
    initTablePersonal();
    resetPersonalForm("add");
});

$(document).on("click", ".addPersonal", function () {
    save_method = "add";
    resetPersonalForm("add");
    $("#modalPersonal").modal("show");
    $(".modal-title").text("Tambah Data Jadwal Pribadi");
});

$(document).on("click", ".editKetersediaanPribadi", function () {
    var id = $(this).data("id");
    showSelectedData(id);
});

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

async function initTablePersonal() {
    personalTable = $("#tablePersonal").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: urls,
            type: "GET",
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
            },
            { data: "nama", name: "nama" },
            { data: "tanggal", name: "tanggal" },
            { data: "waktu_mulai", name: "waktu_mulai" },
            { data: "waktu_selesai", name: "waktu_selesai" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
        order: [[1, "asc"]],
        responsive: true,
        autoWidth: false,
    });
}

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

function createTanggalCard(data = {}) {
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
            "</div>",
    );

    var slotsContainer = card.find(".personal-slots");
    slots.forEach(function (slot) {
        slotsContainer.append(createSlotRow(dateIndex, slot));
    });

    return card;
}

function createSlotRow(dateIndex, slot = {}) {
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
            "</div>",
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
    if (!value) {
        return "";
    }

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
                personalTable.ajax.reload();
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
                }),
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

$(document).on("click", ".deleteKetersediaanPribadi", function () {
    var id = $(this).data("id");

    Swal.fire({
        title: "Konfirmasi Hapus",
        text: "Apakah Anda yakin ingin menghapus data ini?",
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
                        personalTable.ajax.reload();
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
});
