import { remove, clone } from 'lodash'
import Fuse from 'fuse.js'
// import downscale from 'downscale'
import './components/UploadZone'

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