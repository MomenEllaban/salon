import {ref, onMounted} from 'vue'
import { useRequest } from '@/helpers/hooks/useCrudOpration'
import { buildMultiSelectObject } from '@/helpers/utilities'

export const useSelect = async(URL, {value = 'id', label = 'name'}) => {
  let list = []
  let options = []
  const { listingRequest } = useRequest()
  return await listingRequest(URL).then((res) => {
    list = res
    options = buildMultiSelectObject(res, { value: value, label: label })
    return {list, options}
  })
}
