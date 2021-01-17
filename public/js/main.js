$(document).ready(function () {
    documentReady();
    initControls();
    bindGetTaskOnClick();
    initTaskCard();
})

function documentReady() {
    $('#login-btn').click(() => {
        const username = $('#username-inp').val();
        const password = $('#password-inp').val();
        const warningElement = $('#login-error');
        if (parameterValidationForLogin(warningElement, username, password)) {
            return false;
        }

        callAjax('/login', 'POST', {username, password})
            .done(data => {
                switch (data.code) {
                    case 0 : {
                        $('#user-status').text(data.message);
                        warningElement.css('color', 'green');
                        warningElement.text('success')
                        setCookie(data.cookies);
                        hideAdminBtns(false)
                        break;
                    }
                    case -1: {
                        warningElement.css('color', 'red');
                        warningElement.text(data.message)
                        break;
                    }
                }
            })
    })

    $('#create-new-task-btn').click(() => {
        const username = $('#new-task-username').val();
        const email = $('#new-task-email').val();
        const text = $('#new-task-text').val();
        const msgElement = $('#new-task-msg');

        if (parameterValidationForNewTask(msgElement, username, email, text)) {
            return false;
        }

        callAjax('/task', 'POST', {username, email, text})
            .done(data => {
                if (data.code === 0) {
                    msgElement.css('color', 'green');
                } else {
                    msgElement.css('color', 'red');
                }
                msgElement.text(data.message);
                const currentPage = parseInt($('.selected-page-btn').text());
                getTasksByPage(currentPage);
            })
    })

    $('#logout-btn').click(() => {
        callAjax('/logout', 'POST')
            .done(data => {
                switch (data.code) {
                    case 0 : {
                        $("#user-status").text('unknown');
                        removeCookie();
                        hideAdminBtns(true)
                        break;
                    }
                    default: {
                        alert(data.message)
                    }
                }
            })
    })
}

function parameterValidationForLogin(warningElement, username, password) {
    if (!username || !password) {
        warningElement.text('Fill in all the fields.');
        return true;
    }
    return false;
}

function parameterValidationForNewTask(msgElement, username, email, text) {
    if (!username || !email || !text) {
        msgElement.css('color', 'red');
        msgElement.text('Fill in all the fields.');
        return true;
    }

    const re = /\S+@\S+\.\S+/;
    if (!re.test(email)) {
        msgElement.css('color', 'red');
        msgElement.text('Incorrect email form.');
        return true;
    }
    return false;
}

function bindGetTaskOnClick() {
    $('.pag-page-btn').click(function () {
        $('.selected-page-btn').removeClass('selected-page-btn');
        const element = $(this);
        element.addClass('selected-page-btn');
        const pageNumber = element.text();
        getTasksByPage(pageNumber);
    })
}

function getTasksByPage(pageNumber) {
    let sortType = $('.selected-sort').attr('data-sort-type');
    if (!sortType) {
        sortType = '0';
    }
    let sortRevers = $('#sort-revers').is(":checked");

    if (sortRevers) {
        sortRevers = 1;
    } else {
        sortRevers = 0;
    }

    callAjax(`/tasks/${pageNumber}/${sortType}/${sortRevers}`)
        .done(data => {
            if (data.code === 0) {
                const taskList = $('#task-list');
                taskList.empty();
                data.tasks.forEach(task => {
                    createTaskElement(taskList, task.id, task.status, task.text, task.createdAt, task.user, task.email, task.edited);
                })
                updatePaginationBox(data.pagesCount, pageNumber);
                initTaskCard();
            }
        })
}

function createTaskElement(taskList, id, status, text, createdAt, user, email, edited) {
    if (!edited) {
        edited = '';
    }
    taskList.append(
        `<div class="card task">
                    <div class="d-flex justify-content-between align-item-center card-header">
                        <div class="task-id">${id}</div>
                        <div class="task-status">${status}</div>
                        <div class="task-edited">${edited}</div>
                        <div class="task-createAt">${createdAt}</div>
                    </div>
                        <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span>user: </span> <span class="task-userName">${user}</span>
                            </div>
                            <button class="btn btn-sm btn-success send-changes-btn" hidden>send changes</button>
                            <div>
                                <span>e-mail: </span> <span class="task-email">${email}</span>
                            </div>
                        </div>
                        <textarea class="task-text form-control" disabled>${text}</textarea>
                    </div>
                </div>`
    )
}

function updatePaginationBox(pageCount, currentPage) {
    const paginationBox = $('#pagination_box');
    paginationBox.empty();
    for (let i = 1; i <= pageCount; i++) {
        let btn;
        if (i === parseInt(currentPage)) {
            btn = `<button class="pag-page-btn selected-page-btn">${i}</button>`;
        } else {
            btn = `<button class="pag-page-btn">${i}</button>`;
        }
        paginationBox.append(btn)
    }
    bindGetTaskOnClick();
}

function setCookie(value) {
    document.cookie = 'todoUser=' + value + ";";
}

function removeCookie() {
    document.cookie = 'todoUser=;';
}

function initControls() {
    $('.sort-btn').click(function () {
        $('.selected-sort').removeClass('selected-sort');
        $(this).addClass('selected-sort');
    })

    $('#completed-task-btn').click(() => {
        const selectedCard = $('.selected-card');
        const id = selectedCard.find('.task-id').text();
        if (id) {
            callAjax('/task/completed', 'POST', {id})
                .done(data => {
                    switch (data.code) {
                        case 0: {
                            selectedCard.find('.task-status').text('completed')
                            break;
                        }
                        case(-1): {
                            permissionDenied();
                            break;
                        }
                        default: {
                            alert(data.message)
                        }
                    }
                })
        } else {
            alert('select task')
        }
    })

    $('#update-task-btn').click(() => {
        const selectedCard = $('.selected-card');
        const id = selectedCard.find('.task-id').text();
        if (id) {
            const textarea = selectedCard.find('textarea')
            textarea.removeAttr('disabled');
            selectedCard.attr('data-selected-text', textarea.text())
            selectedCard.find('.send-changes-btn').attr('hidden', false)
        } else {
            alert('select task')
        }
    })

    $('#delete_task').click(() => {
            const selectedCard = $('.selected-card');
            const id = selectedCard.find('.task-id').text();
            if (id) {
                if (confirm(`Do you want to delete a task with an index ${id}?`)) {
                    callAjax(`/task/${id}`, 'DELETE')
                        .done(data => {
                            switch (data.code) {
                                case 0: {
                                    const currentPage = parseInt($('.selected-page-btn').text());
                                    getTasksByPage(currentPage);
                                    break;
                                }
                                case(-1): {
                                    permissionDenied();
                                    break;
                                }
                                default: {
                                    alert(data.message)
                                }
                            }
                        })
                }
            } else {
                alert('select task')
            }
        }
    )
}

function initTaskCard() {
    $('.task').click(function () {
        const selectedCard = $('.selected-card');
        const currentElement = $(this);
        const selectedId = selectedCard.find('.task-id').text();
        const currentId = currentElement.find('.task-id').text();
        if (selectedId !== currentId) {
            const textarea = selectedCard.find('textarea');
            selectedCard.find('.send-changes-btn').attr('hidden', true);
            const text = selectedCard.attr('data-selected-text');
            if (text) {
                textarea.val(text)
            }
            textarea.attr('disabled', 'disabled');
            selectedCard.removeClass('selected-card');
            currentElement.addClass('selected-card');
        }
    })

    $('.send-changes-btn').click(function () {
        const currentElement = $(this);
        const selectedTask = currentElement.parent().parent().parent();
        const textarea = selectedTask.find('textarea');
        const text = textarea.val();
        const id = selectedTask.find('.task-id').text();
        callAjax('/task/text', 'POST', {id, text})
            .done(data => {
                switch (data.code) {
                    case 0: {
                        selectedTask.find('.task-edited').text('edited');
                        textarea.attr('disabled', 'disabled');
                        currentElement.attr('hidden', true);
                        break;
                    }
                    case(-1): {
                        permissionDenied();
                        break;
                    }
                    default: {
                        alert(data.message)
                    }
                }
            })
    })
}

function permissionDenied() {
    $("#user-status").text('unknown');
    removeCookie();
    hideAdminBtns(true)
    alert('For this you need to be an administrator. Please log in.')
}

function hideAdminBtns(determinant) {
    hideControlBtns(determinant);
    hideLogBtn(determinant);
}

function hideControlBtns(determinant) {
    $('#delete_task').attr('hidden', determinant);
    $('#completed-task-btn').attr('hidden', determinant);
    $('#update-task-btn').attr('hidden', determinant);
}

function hideLogBtn(determinant) {
    $("#login-modal-btn").attr("hidden", !determinant);
    $("#logout-btn").attr("hidden", determinant);
}

function callAjax(url, method = 'GET', data = {}, dataType = 'json') {
    return $.ajax({
        url: url,
        method: method,
        data: data,
        dataType: dataType
    });
}
