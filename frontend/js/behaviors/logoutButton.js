// POST logout action

const logoutButton = function () {
  const logoutForm = document.querySelector('[data-logout-form]')

  if (!logoutForm) return

  document.body.addEventListener('click', e => {
    if (e.target.hasAttribute('data-logout-btn')) {
      e.preventDefault()
      logoutForm.submit()
    }
  })
}

export default logoutButton
