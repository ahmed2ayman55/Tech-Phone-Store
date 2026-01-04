import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { api, buildUrl } from "@shared/routes";
import { z } from "zod";

export function useOrders() {
  return useQuery({
    queryKey: [api.orders.list.path],
    queryFn: async () => {
      const res = await fetch(api.orders.list.path, { credentials: "include" });
      if (!res.ok) throw new Error("Failed to fetch orders");
      return api.orders.list.responses[200].parse(await res.json());
    },
  });
}

export function useOrder(id: number) {
  return useQuery({
    queryKey: [api.orders.get.path, id],
    queryFn: async () => {
      const url = buildUrl(api.orders.get.path, { id });
      const res = await fetch(url, { credentials: "include" });
      if (!res.ok) throw new Error("Failed to fetch order");
      return api.orders.get.responses[200].parse(await res.json());
    },
  });
}

export function useCreateOrder() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: async (data: z.infer<typeof api.orders.create.input>) => {
      const res = await fetch(api.orders.create.path, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        credentials: "include",
      });
      if (!res.ok) throw new Error("Failed to create order");
      return api.orders.create.responses[201].parse(await res.json());
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [api.orders.list.path] });
    },
  });
}
