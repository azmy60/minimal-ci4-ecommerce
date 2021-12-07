// Usage:
// <input-container class="input-base input-default">
//   <span>prefix</span>
//   <input type="text">
//   <span>suffix</span>
// </input-container>

class InputContainer extends HTMLElement {
  connectedCallback() {
    const input = this.querySelector('input')
    
    input.addEventListener('focus', () => {
      this.classList.add('focused')
    })

    input.addEventListener('blur', () => {
      this.classList.remove('focused')
    })
    
    this.addEventListener('click', () => {
      input.focus()
    })
  }
}

customElements.define('input-container', InputContainer)

export default InputContainer