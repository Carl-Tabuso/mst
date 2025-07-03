export const formatToDateString = (date: string) => {
  return new Date(date).toLocaleDateString('en-ph', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
