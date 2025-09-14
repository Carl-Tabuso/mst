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

export const formatToDateTime = (date: string, time: string) => {
  const [hours, min] = time.split(':')
  const epoch = new Date(date).setHours(Number(hours), Number(min))

  return new Date(epoch)
}
