import downscale from 'downscale'
import UploadZoneThumbnail from './components/UploadZoneThumbnail'

const maxFile = 10
const thumbnailsContainer = document.getElementById('thumbnails-container')
const thumbnailWidth = 110
const thumbnailHeight = 110

const uploadBtn = document.getElementById('upload-btn')

function addThumbnail(file) {
  const thumbnail = new UploadZoneThumbnail()
  const key = Math.random().toString(36).substring(2, 15)
  thumbnail.key = key
  thumbnail.file = file
  thumbnail.width = thumbnailWidth
  thumbnail.height = thumbnailHeight
  thumbnailsContainer.insertBefore(thumbnail, thumbnailsContainer.lastElementChild)
  return key
}

export default uploadZoneData = {
  files: [],
  input: document.querySelector('#photos'),
  maxOverlay: document.querySelector('#maxOverlay'),
  allowedTypes: new Set(['image/jpeg', 'image/png']),
  allowedSize: 2000000, // 2mb
  filesAtMax() {
    return this.files.length >= maxFile
  },
  ondragover(el) {
    el.classList.add('ring', this.filesAtMax() ? 'ring-trueGray-500': 'ring-emerald-500')
  },
  ondragleave(el) {
    el.classList.remove('ring', this.filesAtMax() ? 'ring-trueGray-500': 'ring-emerald-500')
  },
  ondropfiles(droppedFiles, cb = ()=>{}) {
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
      const key = addThumbnail(droppedFiles[i])
      this.files.push({
        key,
        file: droppedFiles[i],
      })
    }
    
    this.input.files = dt.files
    if(this.filesAtMax())
      uploadBtn.classList.add('hidden')
    
    cb()
  },
  removePhoto(key) {
    console.log(key)
    const dt = new DataTransfer()
    const newFiles = []
    for(let i = 0; i < this.files.length; i++) {
      if(this.files[i].key === key){
        console.log('removing', key, this.files[i])
        continue
      }
      dt.items.add(this.files[i].file)
      newFiles.push(this.files[i])
    }
    this.files = newFiles
    this.input.files = dt.files

    if(!this.filesAtMax())
      uploadBtn.classList.remove('hidden')
  }
}