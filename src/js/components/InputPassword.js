// Usage:
// <input-passowrd name="<input-name>" placeholder="<input-placeholder>" placeholder="<sm|base|lg>" ></input-passowrd>

import "./InputContainer"

const templateHtml = /*html*/`
<input-container x-data="{ show: false }">
  <input :type="show ? 'text' : 'password'" x-ref="passwordInput" type="password">
  <div x-cloak @click="show = !show" class="text-trueGray-500">
    <svg x-show="!show" class="fill-current"><use xlink:href="#ph_eye" /></svg>
    <svg x-show="show" class="fill-current"><use xlink:href="#ph_eye-slash" /></svg>
  </div>
</input-container>
`

const template = document.createElement('template')
document.body.appendChild(template)
template.innerHTML = templateHtml

const sizeClasses = {
  'sm': 'input-sm',
  'base': 'input-base',
  'lg': 'input-lg',
}

const colorClasses = {
  'default': 'input-default',
  'danger': 'input-danger',
}

class InputPassword extends HTMLElement {
  getAttr(name, defaultValue = null){
    const value = this.getAttribute(name)
    return value ? value : defaultValue
  }

  connectedCallback() {
    this.appendChild(template.content.cloneNode(true))
    
    const inputContainer = this.querySelector('input-container')
    inputContainer.classList.add(sizeClasses[this.getAttr('size')])
    inputContainer.classList.add(colorClasses[this.getAttr('color')])

    const input = this.querySelector('input')
    input.setAttribute('id', this.getAttr('name'))
    input.setAttribute('name', this.getAttr('name'))
    input.setAttribute('placeholder', this.getAttr('placeholder', ''))
  }
}

customElements.define('input-password', InputPassword)

export default InputPassword