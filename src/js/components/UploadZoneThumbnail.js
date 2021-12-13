import downscale from "downscale"

const templateHtml = /*html*/`
<div class="relative overflow-hidden border cursor-move rounded-lg w-28 h-28">
  <div class="absolute top-0 left-0 z-50 w-full h-full opacity-0 hover:opacity-100">
    <button class="delete-btn absolute bottom-0 right-0 z-50 p-2 rounded-lg bg-trueGray-800">
      <svg class="w-5 h-5 text-white fill-current"><use xlink:href="#ph_trash-simple-bold"></svg>
    </button>
    <span class="absolute w-full h-full bg-trueGray-800 opacity-10"></span>
  </div>
  <img class="w-full h-full">
  <span class="kko w-full h-full absolute left-0 top-0 bg-white bg-opacity-70"></span>
</div>
`
const template = document.createElement('template')
document.body.appendChild(template)
template.innerHTML = templateHtml

class UploadZoneThumbnail extends HTMLElement {
  setImageSource(src) {
    this.querySelector('img').src = src
  }

  constructor(tid, key, file, width, height, removeCb = () => {}) {
    super()

    this.tid = tid
    this.key = key
    this.file = file
    this.width = width
    this.height = height
    this.removeCb = removeCb

    this.originalFileSrc = null
  }

  connectedCallback() {
    this.appendChild(template.content.cloneNode(true))

    const reader = new FileReader()
    reader.onload = _ => {
      downscale(reader.result, this.width, this.height).then(dataurl => {
        this.originalFileSrc = dataurl
        this.setImageSource(dataurl)
      })
    }
    reader.readAsDataURL(this.file)

    this.querySelector('.delete-btn').addEventListener('click', e => {
      e.preventDefault()
      this.removeCb(this, this.key)
      // this.remove()
    })

    this.querySelector('.kko').textContent = this.tid
  }
}

customElements.define('upload-zone-thumbnail', UploadZoneThumbnail)

export default UploadZoneThumbnail