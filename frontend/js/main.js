let apiBaseUrl = "http://localhost/rise-up-crm/api";

async function addUser(event) {
  event.preventDefault();

  const formData = new FormData(event.target);

  try {
    const response = await fetch(`${apiBaseUrl}/users`, {
      method: "POST",
      body: formData,
    });
    if (response.ok) {
      event.target.reset();
    }
    handleResponse(response);
  } catch (e) {
    handleError(e);
  }
}

async function getAllUsers() {
  const tbody = document.querySelector("#users");
  try {
    const response = await fetch(`${apiBaseUrl}/users`, {
      method: "GET",
    });
    const users = await response.json();
    if (Array.isArray(users)) {
      if (users.length > 0) {
        users.forEach((user) => {
          tbody.innerHTML += `
            <tr>
              <td>${user.id}</td>
              <td><div>${user.name}</div></td>
              <td><div>${user.email}</div></td>
              <td><div>${user.tel}</div></td>
              <td><div>${user.address}</div></td>
              <td>
                <div>
                  <button
                    type="button"
                    class="btn submit-button"
                    data-bs-toggle="modal"
                    data-bs-target="#changePasswordModal"
                    data-record-id="${user.id}"
                    onclick="setRecordId(event)"
                  >
                    Change Password
                  </button>
                </div>
              </td>
            </tr>`;
        });
      } else {
        tbody.innerHTML += `
          <tr>
            <td colspan="6">There are no users in the database.</td>
          </tr>`;
      }
    } else {
      handleError(users);
    }
  } catch (e) {
    handleError(e);
  }
}

function setRecordId(event) {
  const modal = document.querySelector("#changePasswordModal");
  modal.dataset.recordId = event.target.dataset.recordId;
}

async function updatePassword(event) {
  event.preventDefault();
  const modal = document.querySelector("#changePasswordModal");
  let recordId = modal.dataset.recordId;

  const formData = new FormData(event.target);

  try {
    const response = await fetch(
      `${apiBaseUrl}/users/${recordId}/update-password`,
      {
        method: "POST",
        body: formData,
      }
    );
    handleResponse(response);
  } catch (e) {
    handleError(e);
  }

  event.target.reset();
}

async function handleResponse(response) {
  let data = await response.json();
  if (response.ok) {
    vNotify.success({ text: data.message, title: "Request successful" });
  } else {
    handleError(data);
  }
}

function handleError(e) {
  if (e.errors) {
    let errors = e.errors;
    for (const field in errors) {
      errors[field].forEach((error) => {
        vNotify.error({ text: error, title: "Validation error" });
      });
    }
  } else {
    console.error(e);
    vNotify.error({
      text: "Some error occurred. Please, contact us at bugreport@crm.com.",
      title: "Server error",
    });
  }
}

function isObject(x) {
  return typeof x === "object" && !Array.isArray(x) && x !== null;
}
