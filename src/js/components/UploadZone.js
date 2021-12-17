// contains 3 inputs:
// photos[] - multiple files
// new_orders - a string of numbers separated with , (comma) or empty string. ex: '0,1,3,4'.
//              represents the order of the files that will be uploaded
// old_orders - same format.
//              represents the order of the files that are added with addThumbnailsFromDB

import Grid from 'muuri'
import UploadZoneThumbnail from './UploadZoneThumbnail'

const templateHtml = /* html */`
<div class="relative w-full h-full rounded-lg">
  <input type="file" multiple name="photos[]" class="hidden">
  <input type="hidden" name="new_orders">
  <input type="hidden" name="old_orders">
  <div class="relative w-full -m-3 thumbnails-container">
    <div class="grid place-content-center w-28 h-28 border rounded-lg border-trueGray-400 cursor-pointer upload-btn">
      <svg class="fill-current w-9 h-9 text-trueGray-400"><use xlink:href="#ph_upload-simple"></svg>
    </div>
  </div>
</div>
`

const template = document.createElement('template')
document.body.appendChild(template)
template.innerHTML = templateHtml

class UploadZone extends HTMLElement {
  constructor() {
    super()

    this.files = []
    this.maxFiles = 10
    this.allowedSize = 2000000 // less than 2MB
    this.allowedTypes = new Set(['image/jpeg', 'image/png'])
    this.thumbnailWidth = 112
    this.thumbnailHeight = 112
    this.thumbnailsContainer = null
    this.uploadBtn = null
    this.muuri = null
    this.root = null
    this._tid = 0
    this.prevPos = -1
    this.inputFiles = null
    this.oldOrdersInput = null
    this.newOrdersInput = null
    this.oldOrders = []
    this.newOrders = []
  }

  isFull() {
    return this.files.length >= this.maxFiles
  }

  addRing() {
    this.root.classList.add('ring', this.isFull() ? 'ring-trueGray-500' : 'ring-emerald-500')
  }

  removeRing() {
    this.root.classList.remove('ring', 'ring-emerald-500', 'ring-emerald-trueGray')
  }

  hideUploadBtn() {
    this.muuri.hide([this.muuri.getItems()[0]])
    // this.uploadBtn.classList.add('hidden')
  }

  showUploadBtn() {
    this.muuri.show([this.muuri.getItems()[0]])
    // this.uploadBtn.classList.remove('hidden')
  }

  getThumbnails() {
    const thumbnails = this.muuri.getItems()
    thumbnails.shift() // remove the upload-btn from the list
    return thumbnails
  }

  reset() {
    this._tid = 0
    this.prevPos = -1
    this.files = []
    this.oldOrders = []
    this.newOrders = []
    this.inputFiles.value = ''
    this.oldOrdersInput.value = ''
    this.newOrdersInput.value = ''
    this.muuri.remove(this.getThumbnails(), { removeElements: true })
  }

  refreshGrid() {
    this.muuri.refreshItems().layout()
  }

  tidCounter() {
    return this._tid++; 
  }

  makeOrder(tid, order) {
    return { tid, order }
  }

  findIndexOldOrder(tid) {
    return this.oldOrders.findIndex((o) => o.tid === tid)
  }

  findIndexNewOrder(tid) {
    return this.newOrders.findIndex((o) => o.tid === tid)
  }

  updateOldOrdersInput() {
    this.oldOrdersInput.value = this.oldOrders.map((o) => o.order).join(',')
  }

  updateNewOrdersInput() {
    this.newOrdersInput.value = this.newOrders.map((o) => o.order).join(',')
  }
  
  resetOrder() {
    this.files.forEach((file, i) => {
      this.files[i].order = i
      file.thumbnail.updateOrder(i) 

      if(file.isFromDB) {
        const i = this.findIndexOldOrder(file.tid)
        if(i !== -1)  this.oldOrders[i].order = file.order
      }
      else {
        const i = this.findIndexNewOrder(file.tid)
        if(i !== -1)  this.newOrders[i].order = file.order
      }
    })

    this.updateOldOrdersInput()
    this.updateNewOrdersInput()
  }

  addFile(tid, file, order, isFromDB) {
    const thumbnail = new UploadZoneThumbnail(tid, order, file, this.thumbnailWidth, this.thumbnailHeight, this.removeFile.bind(this))
    this.files.push({ tid, file, order, isFromDB, thumbnail })
    this.muuri.add([thumbnail], { index: this.files.length })
  }

  removeFile(tid) {
    const dt = new DataTransfer()
    const keepFiles = []

    this.files.forEach((file) => {
      if(file.tid !== tid) {
        if(!file.isFromDB) dt.items.add(file.file)
        return keepFiles.push(file) 
      }

      const item = this.getThumbnails().find((item) => item.getElement().tid === tid)
      this.muuri.remove([item], { removeElements: true })

      if(file.isFromDB) {
        const i = this.findIndexOldOrder(tid)
        if(i !== -1) this.oldOrders.splice(i, 1)
      }
      else {
        const i = this.findIndexNewOrder(tid)
        if(i !== -1) this.newOrders.splice(i, 1)
      }
    })

    this.files = keepFiles

    this.resetOrder()

    this.inputFiles.files = dt.files

    if(!this.isFull()) this.showUploadBtn()
  }

  /**
   * @param {Array} thumbnails - an array of object containing img source and order { src: <string>, order: <number|null> } 
   */
  addThumbnailsFromDB(thumbnails) {
    thumbnails.forEach((thumbnail) => {
      const tid = this.tidCounter()
      this.addFile(tid, thumbnail.src, thumbnail.order, true)
      this.oldOrders.push(this.makeOrder(tid, thumbnail.order))
    })

    this.updateOldOrdersInput()

    if(this.isFull()) this.hideUploadBtn()
  }

  getDataTransferFromFiles() {
    const dt = new DataTransfer()
    this.files.forEach((file) => !file.isFromDB ? dt.items.add(file.file) : null)
    return dt
  }

  onDropFiles(event) {
    const droppedFiles = event.dataTransfer ? event.dataTransfer.files : event.target.files;

    if(droppedFiles.length === 0) return

    if(this.isFull()) return // TODO: show message (cannot add more than 10 photos)

    const dt = this.getDataTransferFromFiles()

    for(let i = 0; i < droppedFiles.length; i++) {
      // File validation
      if(dt.files.length >= this.maxFiles)
        break // TODO: show message (cannot add more than 10 photos)
      if(!this.allowedTypes.has(droppedFiles[i].type)) // TODO: use built-in accept attribute in input tag
        continue // TODO: show message (unsupported file type)
      if(droppedFiles[i].size >= this.allowedSize)
        continue // TODO: show message (unsupported file size) (NEED TEST)
      
      // Register a new file to droppedFiles
      dt.items.add(droppedFiles[i])

      // Register a new file to files
      const tid = this.tidCounter()
      this.addFile(tid, droppedFiles[i], null, false)
      this.newOrders.push(this.makeOrder(tid, null))
    }
    
    this.inputFiles.files = dt.files

    this.updateNewOrdersInput()

    if(this.isFull()) this.hideUploadBtn()
  }

  connectedCallback() {
    this.appendChild(template.content.cloneNode(true))
    this.root = this.children[0]
    this.inputFiles = this.root.querySelector('input[type="file"]')
    this.oldOrdersInput = this.root.querySelector('input[name="old_orders"]')
    this.newOrdersInput = this.root.querySelector('input[name="new_orders"]')
    this.thumbnailsContainer = this.root.querySelector('.thumbnails-container')
    this.uploadBtn = this.root.querySelector('.upload-btn')

    this.inputFiles.addEventListener('change', this.onDropFiles.bind(this))
    
    this.uploadBtn.addEventListener('click', () => { this.inputFiles.click() })

    this.muuri = new Grid(this.thumbnailsContainer, {
      dragEnabled: true,
      dragStartPredicate: (thumbnail, event) => {
        if(!event.target.closest('upload-zone-thumbnail'))
            return false
        return Grid.ItemDrag.defaultStartPredicate(thumbnail, event)
      },
      dragSortPredicate: (thumbnail) => {
        const result = Grid.ItemDrag.defaultSortPredicate(thumbnail)
        return result && result.index === 0 ? false : result
      },
    })

    this.muuri.on('dragStart', (thumbnail) => {
      const itemId = thumbnail.getElement().tid
      const thumbnails = this.getThumbnails()
      this.prevPos = thumbnails.findIndex((th) => th.getElement().tid === itemId)
    })
  
    this.muuri.on('dragReleaseEnd', (thumbnail) => {
      // Sort element based on uploadZoneThumbnail.tid
      
      const itemId = thumbnail.getElement().tid
      const thumbnails = this.getThumbnails()
            
      const currentPos = thumbnails.findIndex((th) => th.getElement().tid === itemId)
      
      if(currentPos === this.prevPos) // no swapping
        return
  
      const slicedFiles = this.files.splice(this.prevPos, 1)
  
      this.files.splice(currentPos, 0, slicedFiles[0])

      this.resetOrder()
  
      this.inputFiles.files = this.getDataTransferFromFiles().files
    });

    this.addEventListener('dragover', (e) => {
      e.preventDefault()
      
      this.addRing()
    })

    this.addEventListener('dragleave', (e) => {
      e.preventDefault()
      
      this.addRing()
    })

    this.addEventListener('drop', (e) => {
      e.preventDefault()

      this.removeRing()
      this.onDropFiles(e)
    })
  }
}

customElements.define('upload-zone', UploadZone)

export default UploadZone
