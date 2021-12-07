// Usage:
// <input-container class="input-base input-default">
//   <span>prefix</span>
//   <input type="text">
//   <span>suffix</span>
// </input-container>

class InputContainer extends HTMLElement {
  connectedCallback() {
    const input = this.querySelector('input')
    
    input.addEventListener('focus', e => {
      this.classList.add('focused')
    })

    input.addEventListener('blur', e => {
      this.classList.remove('focused')
    })
    
    this.addEventListener('click', e => {
      input.focus()
    })
  }
}

customElements.define('input-container', InputContainer)

export default InputContainer