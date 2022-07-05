import { Card, DataTable, Page, Layout, SkeletonBodyText } from '@shopify/polaris';
import { Loading, TitleBar } from '@shopify/app-bridge-react';
import { useAppQuery } from '../../hooks';
import { useParams } from 'react-router-dom';

export default function ProductDetail() {
    const { id } = useParams();

    const {
        data,
        isLoading,
        isRefetching
    } = useAppQuery({
        url: `/api/product/${id}`
    })

    const breadcrumbs = [{content: 'Customers and Products', url: '/' }];

    if (isLoading || isRefetching) {
        return (
            <Page>
                <TitleBar title="Product Display" breadcrumbs={breadcrumbs}
                primaryAction={null} />
                <Loading />
                <Layout>
                    <Layout.Section>
                        <Card sectioned title="Product">
                            <SkeletonBodyText />
                        </Card>
                    </Layout.Section>
                </Layout>
            </Page>
        )
    }

    let rows = [];

    data.forEach((dataPiece) => {
        rows.push([
            dataPiece.title,
            dataPiece.vendor,
            dataPiece.type,
            dataPiece.price
        ])
    });

    return (
        <Page>
            <TitleBar title="Product Title" breadcrumbs={breadcrumbs}
                primaryAction={null} />
            <DataTable
                columnContentTypes = {[
                    "text",
                    "text",
                    "text",
                    "numeric",
                ]}
                headings={[
                    "Title",
                    "Vendor",
                    "Type",
                    "Price",
                ]}
                rows={rows}
                loading={isLoading}
            />

        </Page>
        
    )
}