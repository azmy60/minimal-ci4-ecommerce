import downscale from "downscale"

const templateHtml = /*html*/`
<div class="relative overflow-hidden border rounded-lg cursor-pointer w-28 h-28">
  <div class="absolute top-0 left-0 z-50 w-full h-full opacity-0 hover:opacity-100">
    <button class="delete-btn absolute bottom-0 right-0 z-50 p-2 rounded-lg bg-trueGray-800">
      <svg class="w-5 h-5 text-white fill-current"><use xlink:href="#ph_trash-simple-bold"></svg>
    </button>
    <span class="absolute w-full h-full bg-trueGray-800 opacity-10"></span>
  </div>
  <img class="w-full h-full">
</div>
`
const template = document.createElement('template')
document.body.appendChild(template)
template.innerHTML = templateHtml

class UploadZoneThumbnail extends HTMLElement {
  setImageSource(src) {
    this.querySelector('img').src = src
  }

  constructor() {
    super()

    this.key = null
    this.file = null
    this.originalFileSrc = null
    this.width = null
    this.height = null
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
      uploadZoneData.removePhoto(this.key)
      this.remove()
    })
  }
}

customElements.define('upload-zone-thumbnail', UploadZoneThumbnail)

export default UploadZoneThumbnail