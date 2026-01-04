import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { api, buildUrl } from "@shared/routes";
import { z } from "zod";

export function useReviews(productId: number) {
  return useQuery({
    queryKey: [api.reviews.list.path, productId],
    queryFn: async () => {
      const url = buildUrl(api.reviews.list.path, { productId });
      const res = await fetch(url);
      if (!res.ok) throw new Error("Failed to fetch reviews");
      return api.reviews.list.responses[200].parse(await res.json());
    },
  });
}

export function useCreateReview() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: async ({ productId, ...data }: { productId: number } & z.infer<typeof api.reviews.create.input>) => {
      const url = buildUrl(api.reviews.create.path, { productId });
      const res = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to submit review");
      return api.reviews.create.responses[201].parse(await res.json());
    },
    onSuccess: (_, variables) => {
      const url = buildUrl(api.reviews.list.path, { productId: variables.productId });
      queryClient.invalidateQueries({ queryKey: [url] });
    },
  });
}
