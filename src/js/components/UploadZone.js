// contains 4 inputs:
// photos[] - multiple files
// photos_orders  - a json string consisting of an array of numbers. ex: '[1,2,4]'.
//                  representing the order of each file in input photos[] accordingly.
// db_orders      - a json string consisting of an array of objects with id and order as the attributes. ex: '[{ "id": 0, "order": 4 }]'.
//                  corresponds to the array that is added with addThumbnailsFromDB.
// delete_ids     - same as photos_orders.
//                  the ids correspond to the ids that are passed in addThumbnailsFromDB.

import Grid from 'muuri'
import UploadZoneThumbnail from './UploadZoneThumbnail'

const templateHtml = /* html */`
<div class="relative w-full h-full rounded-lg">
  <input type="file" multiple class="hidden for_file_selection_dialog">
  <input type="file" multiple name="photos[]" class="hidden">
  <input type="hidden" name="photos_orders">
  <input type="hidden" name="db_orders">
  <input type="hidden" name="delete_ids">
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
    this.filesInput = null
    this.dbOrdersInput = null
    this.photosOrdersInput = null
    this.deleteIdsInput = null
    this.deleteIds = []
  }

  isFull() {
    return this.files.length >= this.maxFiles
  }

  isFileFromDB(file) {
    return file.id !== null
  }

  addRing() {
    this.root.classList.add('ring', this.isFull() ? 'ring-trueGray-500' : 'ring-emerald-500')
  }

  removeRing() {
    this.root.classList.remove('ring', 'ring-emerald-500', 'ring-emerald-trueGray')
  }

  hideUploadBtn() {
    this.muuri.hide([this.muuri.getItems().pop()])
  }

  showUploadBtn() {
    this.muuri.show([this.muuri.getItems().pop()])
  }

  getThumbnails() {
    const thumbnails = this.muuri.getItems()
    thumbnails.pop() // remove the upload-btn from the list
    return thumbnails
  }

  reset() {
    this._tid = 0
    this.prevPos = -1
    this.files = []
    this.deleteIds = []
    this.ffsdInput.value = ''
    this.filesInput.value = ''
    this.dbOrdersInput.value = ''
    this.photosOrdersInput.value = ''
    this.muuri.remove(this.getThumbnails(), { removeElements: true })
  }

  refreshGrid() {
    this.muuri.refreshItems().layout()
  }

  tidCounter() {
    return this._tid++; 
  }

  updateDbOrdersInput() {
    const orders = this.files
      .filter((file) => this.isFileFromDB(file))
      .map((file) => { return { id: file.id, order: file.order } })
    this.dbOrdersInput.value = JSON.stringify(orders)
  }

  updatePhotosOrdersInput() {
    const orders = this.files
      .filter((file) => !this.isFileFromDB(file))
      .map((file) => file.order)
    this.photosOrdersInput.value = JSON.stringify(orders)
  }
  
  resetOrder() {
    this.files.forEach((file, i) => {
      this.files[i].order = i
      file.thumbnail.updateOrder(i) 
    })

    this.updateDbOrdersInput()
    this.updatePhotosOrdersInput()
  }

  addFile(id, tid, file) {
    if(this.files.length === 0)
      this.dispatchEvent(new CustomEvent('uploadzonefilled', { bubbles: true, composed: true, cancelable: true }))
    
    const order = -1

    const thumbnail = new UploadZoneThumbnail(tid, order, file, this.thumbnailWidth, this.thumbnailHeight, this.removeFile.bind(this))
    const muuriItem = this.muuri.add([thumbnail], { index: this.files.length })[0]
    this.files.push({ id, tid, file, order, thumbnail, muuriItem })
  }

  removeFile(tid) {
    const dt = new DataTransfer()

    this.files = this.files.filter((file) => {
      if(file.tid !== tid) {
        if(!this.isFileFromDB(file)) dt.items.add(file.file)
        return true // keep this file
      }

      this.muuri.remove([file.muuriItem], { removeElements: true })

      if(this.isFileFromDB(file)) this.deleteIds.push(file.id)

      return false // remove this file
    })

    this.resetOrder()

    this.deleteIdsInput.value = JSON.stringify(this.deleteIds)

    this.filesInput.files = dt.files

    if(!this.isFull()) this.showUploadBtn()

    if(this.files.length === 0)
      this.dispatchEvent(new CustomEvent('uploadzoneemptied', { bubbles: true, composed: true, cancelable: true }))
  }

  /**
   * @param {Array} files - an array of object containing img source and order { id: <number>, src: <string> } 
   */
  addFilesFromDB(files) {
    files.forEach((file) => {
      const tid = this.tidCounter()
      this.addFile(file.id, tid, file.src)
    })

    this.resetOrder()

    if(this.isFull()) this.hideUploadBtn()
  }

  getDataTransferFromFiles() {
    const dt = new DataTransfer()
    this.files.forEach((file) => !this.isFileFromDB(file) ? dt.items.add(file.file) : null)
    return dt
  }

  onDropFiles(event) {
    const droppedFiles = event.dataTransfer ? event.dataTransfer.files : event.target.files;

    if(droppedFiles.length === 0) return

    if(this.isFull()) return // TODO: show message (cannot add more than 10 photos)

    const dt = this.getDataTransferFromFiles()

    for(let i = 0; i < droppedFiles.length; i++) {
      // File validation
      if(this.isFull())
        break // TODO: show message (cannot add more than 10 photos)
      if(!this.allowedTypes.has(droppedFiles[i].type)) // TODO: use built-in accept attribute in input tag
        continue // TODO: show message (unsupported file type)
      if(droppedFiles[i].size >= this.allowedSize)
        continue // TODO: show message (unsupported file size) (NEED TEST)
      
      // Register a new file to droppedFiles
      dt.items.add(droppedFiles[i])

      // Register a new file to files
      const tid = this.tidCounter()
      this.addFile(null, tid, droppedFiles[i])
    }

    this.resetOrder()
    
    this.filesInput.files = dt.files

    this.ffsdInput.value = ''

    if(this.isFull()) this.hideUploadBtn()
  }

  connectedCallback() {
    this.appendChild(template.content.cloneNode(true))
    this.root = this.children[0]
    this.filesInput = this.root.querySelector('input[name="photos[]"]')
    this.ffsdInput = this.root.querySelector('.for_file_selection_dialog')
    this.dbOrdersInput = this.root.querySelector('input[name="db_orders"]')
    this.photosOrdersInput = this.root.querySelector('input[name="photos_orders"]')
    this.deleteIdsInput = this.root.querySelector('input[name="delete_ids"]')
    this.thumbnailsContainer = this.root.querySelector('.thumbnails-container')
    this.uploadBtn = this.root.querySelector('.upload-btn')

    this.ffsdInput.addEventListener('change', this.onDropFiles.bind(this))
    
    this.uploadBtn.addEventListener('click', () => { this.ffsdInput.click() })

    this.muuri = new Grid(this.thumbnailsContainer, {
      dragEnabled: true,
      dragStartPredicate: (thumbnail, event) => {
        if(!event.target.closest('upload-zone-thumbnail'))
            return false
        return Grid.ItemDrag.defaultStartPredicate(thumbnail, event)
      },
      dragSortPredicate: (thumbnail) => {
        const result = Grid.ItemDrag.defaultSortPredicate(thumbnail)
        return result && result.index === this.files.length ? false : result
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
  
      this.filesInput.files = this.getDataTransferFromFiles().files
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
