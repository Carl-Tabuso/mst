<script lang="ts">
import { reactive } from 'vue'

export function useCoworkerRatings() {
  const ratings = reactive<
    Record<
      number,
      {
        evaluatee_id: number
        performance_rating_id: number
        description: string
      }[]
    >
  >({})

  function setRating(
    form3Id: number,
    coworkerId: number,
    value: number | string,
  ) {
    if (!ratings[form3Id]) ratings[form3Id] = []
    const r = ratings[form3Id].find((r) => r.evaluatee_id === coworkerId)
    if (r) {
      r.performance_rating_id = Number(value)
    } else {
      ratings[form3Id].push({
        evaluatee_id: coworkerId,
        performance_rating_id: Number(value),
        description: '',
      })
    }
  }

  function setDescription(form3Id: number, coworkerId: number, value: string) {
    if (!ratings[form3Id]) ratings[form3Id] = []
    const r = ratings[form3Id].find((r) => r.evaluatee_id === coworkerId)
    if (r) {
      r.description = value
    } else {
      ratings[form3Id].push({
        evaluatee_id: coworkerId,
        performance_rating_id: 0,
        description: value,
      })
    }
  }

  return {
    ratings,
    setRating,
    setDescription,
  }
}
</script>
