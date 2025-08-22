import { format } from 'date-fns'

export const formatToDateString = (date: string) => {
  return new Date(date).toLocaleDateString('en-ph', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

export const formatToDateDisplay = (date: any, options: any) => {
  return format(new Date(date), options)
}
