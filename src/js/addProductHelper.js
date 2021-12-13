import { remove, clone } from 'lodash'
import Fuse from 'fuse.js'
// import downscale from 'downscale'
import UploadZoneThumbnail from './components/UploadZoneThumbnail'

const maxFile = 10
// const thumbnailsContainer = document.getElementById('thumbnails-container')
const thumbnailWidth = 110
const thumbnailHeight = 110

const uploadBtn = document.getElementById('upload-btn')

export const uploadZoneData = {
  files: [],
  muuri: null,
  prevPos: null,
  input: document.querySelector('#photos'),
  maxOverlay: document.querySelector('#maxOverlay'),
  allowedTypes: new Set(['image/jpeg', 'image/png']),
  allowedSize: 2000000, // 2mb
  _tid: 0,
  tidCounter() {
    return _.clone(this._tid++)
  },
  filesAtMax() {
    return this.files.length >= maxFile
  },
  ondragover(el) {
    el.classList.add('ring', this.filesAtMax() ? 'ring-trueGray-500': 'ring-emerald-500')
  },
  ondragleave(el) {
    el.classList.remove('ring', this.filesAtMax() ? 'ring-trueGray-500': 'ring-emerald-500')
  },
  ondropfiles(event, droppedFiles, cb = ()=>{}) {
    if(event.type == 'drop' && droppedFiles.length === 0)
      return cb()

    if(this.filesAtMax())
      return cb() // TODO: show message (cannot add more than 10 photos)

    const dt = new DataTransfer()
    for(let i = 0; i < this.files.length; i++) {
      dt.items.add(this.files[i].file)
    }

    for(let i = 0; i < droppedFiles.length; i++) {
      // File validation
      if(dt.files.length >= maxFile)
        break // TODO: show message (cannot add more than 10 photos)
      if(!this.allowedTypes.has(droppedFiles[i].type))
        continue // TODO: show message (unsupported file type)
      if(droppedFiles[i].size >= this.allowedSize)
        continue // TODO: show message (unsupported file size) (NEED TEST)
      
      // Register a new file to droppedFiles
      dt.items.add(droppedFiles[i])

      // Generate a thumbnail
      const tid = this.tidCounter()
      const key = this._addThumbnail(tid, droppedFiles[i])
      this.files.push({
        key,
        tid: tid,
        file: droppedFiles[i],
      })
    }
    
    this.input.files = dt.files
    if(this.filesAtMax())
      uploadBtn.classList.add('hidden')

    cb()
  },
  removePhoto(thumbnail, key) {
    console.log(key)
    const dt = new DataTransfer()
    const newFiles = []
    for(let i = 0; i < this.files.length; i++) {
      if(this.files[i].key === key){
        console.log('removing', key, this.files[i])
        const tid = this.files[i].tid
        const item = this.muuri.getItems().find((item) => item.getElement().tid === tid)
        this.muuri.remove([item], { removeElements: true })
        continue
      }
      dt.items.add(this.files[i].file)
      newFiles.push(this.files[i])
    }
    this.files = newFiles
    this.input.files = dt.files

    if(!this.filesAtMax())
      uploadBtn.classList.remove('hidden')
  },
  refreshGrid() {
    this.muuri.refreshItems().layout();
  },
  _addThumbnail(tid, file) {
    const key = Math.random().toString(36).substring(2, 15)
    const thumbnail = new UploadZoneThumbnail(tid, key, file, thumbnailWidth, thumbnailHeight, this.removePhoto.bind(this))
    this.muuri.add([thumbnail], { index: this.files.length })
    return key
  }
}

export function addCatData(json) {
  const fuse = new Fuse(json, {
    keys: ['name'],
  })

  return {
    show: false,
    cats: json.slice(0),
    selectedCats: [],
    inputVal: '',
    _newId: -1,
    newIdCounter(){
       return this._newId--
    },
    searchCat(q) {
      this.cats = q === '' ? json : fuse.search(q).map(c => c.item)
    },
    addCat(c) {
      this.selectedCats.push(c)
      this.cats = json.filter(doc => doc.id !== c.id)
      if(c.id >= 0)
        fuse.remove(doc => doc.id === c.id)
    },
    removeCat(c) {
      remove(this.selectedCats, sc => sc.id === c.id)
      if(c.id >= 0) { // add the cat as a search suggestion if it is from database
        this.cats.push(c)
        fuse.add(c)
      }
    },
  }
}