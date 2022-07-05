import { Card, DataTable, SkeletonBodyText } from "@shopify/polaris";
import { Loading } from '@shopify/app-bridge-react';
import { useAppQuery } from "../hooks";

export function CustomerCard() {
    const {
        data,
        isLoading,
        isRefetching
    } = useAppQuery({
        url: "api/customers"
    });

    if (isLoading || isRefetching) {
        return (
            <Card sectioned title="Customers">
                <Loading />
                <SkeletonBodyText />
            </Card>
        )
    }

    let rows = [];

    data.forEach((dataPiece) => {
        rows.push([
            dataPiece.first_name,
            dataPiece.last_name,
            dataPiece.email,
            dataPiece.num_orders,
            dataPiece.net_sales
        ])
    });

    return (
        <Card title="Customers">
            <DataTable
                columnContentTypes = {[
                    "text",
                    "text",
                    "text",
                    "numeric",
                    "numeric"
                ]}
                headings={[
                    "First name",
                    "Last name",
                    "Email",
                    "Number of orders",
                    "Net sales"
                ]}
                rows={rows}
                loading={isLoading}
            />
        </Card>
    )
}