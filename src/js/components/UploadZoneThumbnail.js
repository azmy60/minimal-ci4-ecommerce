import downscale from "downscale"

const templateHtml = /*html*/`
<div class="relative overflow-hidden border cursor-move rounded-lg w-28 h-28">
  <div class="absolute top-0 left-0 z-50 w-full h-full opacity-0 hover:opacity-100">
    <button class="delete-btn absolute bottom-0 right-0 z-50 p-2 rounded-lg bg-neutral-800">
      <svg class="w-5 h-5 text-white fill-current"><use xlink:href="#ph_trash-simple-bold"></svg>
    </button>
    <span class="absolute w-full h-full bg-neutral-800 opacity-10"></span>
  </div>
  <img width="112" height="112" class="w-full h-full">
</div>
`
const template = document.createElement('template')
document.body.appendChild(template)
template.innerHTML = templateHtml

class UploadZoneThumbnail extends HTMLElement {
  setImageSource(src) {
    this.querySelector('img').src = src
  }

  constructor(tid, order, file, width, height, removeCb = () => {}) {
    super()

    this.tid = tid
    this.order = order
    this.file = file
    this.width = width
    this.height = height
    this.removeCb = removeCb

    this.originalFileSrc = null
  }

  updateOrder(order) {
    this.order = order
  }

  connectedCallback() {
    this.appendChild(template.content.cloneNode(true))

    if(typeof(this.file) === 'string') {
      this.setImageSource(this.file)
    } else {
      const reader = new FileReader()
      reader.onload = _ => {
        downscale(reader.result, this.width, this.height).then(dataurl => {
          this.originalFileSrc = dataurl
          this.setImageSource(dataurl)
        })
      }
      reader.readAsDataURL(this.file)
    }


    this.querySelector('.delete-btn').addEventListener('click', e => {
      e.preventDefault()
      this.removeCb(this.tid)
      // this.remove()
    })
  }
}

customElements.define('upload-zone-thumbnail', UploadZoneThumbnail)

export default UploadZoneThumbnail