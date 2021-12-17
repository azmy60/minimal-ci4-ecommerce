import './components/InputContainer'
import ClipboardJS from 'clipboard'
import Fuse from 'fuse.js'
import { remove } from 'lodash'
import './components/UploadZone'

new ClipboardJS('.btn-copy')

export function addCatData(allCats, selectedCats) {
  let unselectedCats = allCats.filter(ac => !selectedCats.some(sc => sc.id === ac.id))

  const fuse = new Fuse(unselectedCats, {
    keys: ['name'],
    ignoreLocation: true,
    threshold: 0,
  })

  return {
    show: false,
    dropdownCats: unselectedCats.slice(0),
    updatedCats: selectedCats.map(sc => {
      sc.remove = 0
      return sc
    }),
    inputVal: '',
    _newId: -1,
    newIdCounter() {
       return this._newId--
    },
    searchCat(q) {
      this.dropdownCats = q === '' ? this.dropdownEmptySearch() : fuse.search(q).map(c => c.item)
    },
    addCat(cat) {
      fuse.remove(doc => doc.id === cat.id)
      
      if(this.isFromSelected(cat.id))
        this.updateRemove(cat.id, 0)
      else
        this.updatedCats.push(cat)
      
      this.dropdownCats = this.dropdownEmptySearch()
    },
    addNewCat(name) {
      this.updatedCats.push({
        id: this.newIdCounter(),
        name,
      })
    },
    removeCat(cat) {
      if(cat.id < 0) { // remove from list if the category is not from the database
        remove(this.updatedCats, uc => uc.id === cat.id)
      } else {
        fuse.add(cat)

        if(this.isFromSelected(cat.id))
          this.updateRemove(cat.id, 1)
        else
          remove(this.updatedCats, uc => uc.id === cat.id)
        
        this.dropdownCats = this.dropdownEmptySearch()
      }
    },
    updateRemove(id, value) {
      this.updatedCats.some((uc, i) => {
        if(uc.id === id)
          this.updatedCats[i].remove = value
        return uc.id === id
      })
    },
    dropdownEmptySearch() {
      const excludes = this.updatedCats.reduce((prev, curr) => {
        if(curr.remove === 0 || !this.isFromSelected(curr.id))
          prev.push(curr.id)
        return prev
      }, [])
      return allCats.filter(ac => !excludes.includes(ac.id))
    },
    isFromSelected(id) {
      return selectedCats.some(uc => uc.id === id)
    },
  }
}